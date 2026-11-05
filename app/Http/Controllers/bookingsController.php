<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class bookingsController extends Controller
{
    private const OPEN_BOOKING_STATUSES = ['pending', 'confirmed', 'active'];

    public function listBookings(Request $request)
    {
        $this->authorize('viewAny', Bookings::class);
        $selectedCustomer = null;

        if ($request->user()->role === 'admin' && $request->filled('customer_id')) {
            $selectedCustomer = User::where('role', 'user')->findOrFail($request->query('customer_id'));
        }

        $bookingsQuery = Bookings::with(['customer', 'car']);
        if (auth()->user()->role !== 'admin') {
            $bookingsQuery->where('customer_id', auth()->id());
        }
        $bookings = $bookingsQuery->get();
        $carsQuery = Car::orderBy('brand');
        if (auth()->user()->role !== 'admin') {
            $carsQuery->where('status', 'available');
        }
        $cars = $carsQuery->get();
        $customersQuery = User::where('role', 'user')->orderBy('name');
        if (auth()->user()->role !== 'admin') {
            $customersQuery->whereKey(auth()->id());
        }
        $customers = $customersQuery->get();

        return view("bookings.list", compact('bookings', 'cars', 'customers', 'selectedCustomer'));
    }

    public function edit(Bookings $booking)
    {
        $this->authorize('update', $booking);

        $carsQuery = Car::orderBy('brand');
        if (auth()->user()->role !== 'admin') {
            $carsQuery->where(function ($query) use ($booking) {
                $query->where('status', 'available')
                    ->orWhereKey($booking->car_id);
            });
        }
        $cars = $carsQuery->get();
        $customersQuery = User::where('role', 'user')->orderBy('name');
        if (auth()->user()->role !== 'admin') {
            $customersQuery->whereKey(auth()->id());
        }
        $customers = $customersQuery->get();

        return view('bookings.edit', compact('booking', 'cars', 'customers'));
    }

    public function show(Bookings $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['customer', 'car']);

        return view('bookings.show', compact('booking'));
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Bookings::class);

        $bookings = Bookings::with(['customer', 'car']);
        if ($request->user()->role !== 'admin') {
            $bookings->where('customer_id', $request->user()->id);
        }

        return response()->json($bookings->get());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Bookings::class);

        $isAdmin = $request->user()->role === 'admin';
        $validated = $request->validate([
            'car_id' => [
                'required',
                $isAdmin ? 'exists:cars,id' : Rule::exists('cars', 'id')->where('status', 'available'),
            ],
            'customer_id' => $isAdmin
                ? ['nullable', Rule::exists('users', 'id')->where('role', 'user')]
                : ['prohibited'],
            'customer_name' => [$isAdmin ? 'required_without:customer_id' : 'nullable', 'string', 'max:255'],
            'customer_email' => [$isAdmin ? 'required_without:customer_id' : 'nullable', 'email', 'max:255'],
            'customer_phone' => [$isAdmin ? 'required_without:customer_id' : 'nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        DB::transaction(function () use ($validated, $isAdmin, $request) {
            $car = Car::whereKey($validated['car_id'])->lockForUpdate()->firstOrFail();
            if ($isAdmin) {
                if (!empty($validated['customer_id'])) {
                    $customer = User::where('role', 'user')->findOrFail($validated['customer_id']);
                } else {
                    $customer = User::firstOrNew(['email' => $validated['customer_email']]);
                    if ($customer->exists && $customer->getAttribute('role') !== 'user') {
                        throw ValidationException::withMessages([
                            'customer_email' => 'This email is already associated with a non-customer account.',
                        ]);
                    }

                    $customerAttributes = [
                        'name' => $validated['customer_name'],
                        'phone' => $validated['customer_phone'],
                        'address' => $validated['customer_address'] ?? null,
                    ];
                    if (!$customer->exists) {
                        $customerAttributes['password'] = Str::random(64);
                        $customerAttributes['role'] = 'user';
                    }
                    $customer->fill($customerAttributes)->save();
                }
            } else {
                $customer = $request->user();
            }

            $days = Carbon::parse($validated['start_date'])
                ->diffInDays(Carbon::parse($validated['end_date']));

            Bookings::create([
                'car_id' => $car->id,
                'customer_id' => $customer->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_price' => $days * $car->daily_rate,
            ]);

            $this->syncCarAvailability($car->id);
        });

        return redirect()->route('show_booking_list')
            ->with('success', 'Booking created successfully.');
    }

    public function update(Request $request, Bookings $booking)
    {
        $this->authorize('update', $booking);

        $isAdmin = $request->user()->role === 'admin';
        $validated = $request->validate([
            'car_id' => [
                'required',
                $isAdmin
                    ? 'exists:cars,id'
                    : Rule::exists('cars', 'id')->where(fn ($query) => $query
                        ->where('status', 'available')
                        ->orWhere('id', $booking->car_id)),
            ],
            'customer_id' => $isAdmin
                ? ['required', Rule::exists('users', 'id')->where('role', 'user')]
                : ['prohibited'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:pending,confirmed,active,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Bookings::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $previousCarId = $booking->car_id;
            $car = Car::whereKey($validated['car_id'])->lockForUpdate()->firstOrFail();
            $days = Carbon::parse($validated['start_date'])
                ->diffInDays(Carbon::parse($validated['end_date']));
            $validated['total_price'] = $days * $car->daily_rate;

            $booking->update($validated);

            $this->syncCarAvailability($previousCarId);
            $this->syncCarAvailability($car->id);
        });

        return redirect()->route('show_booking_list')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Bookings $booking)
    {
        $this->authorize('delete', $booking);

        DB::transaction(function () use ($booking) {
            $booking = Bookings::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $carId = $booking->car_id;
            $booking->delete();

            $this->syncCarAvailability($carId);
        });

        return redirect()->route('show_booking_list')->with('success', 'Booking deleted successfully.');
    }

    private function syncCarAvailability(int $carId): void
    {
        $car = Car::findOrFail($carId);
        $hasOpenBooking = $car->bookings()
            ->whereIn('status', self::OPEN_BOOKING_STATUSES)
            ->exists();

        $car->update(['status' => $hasOpenBooking ? 'rented' : 'available']);
    }
}
