<?php

namespace App\Http\Controllers;

use App\Mail\BookingApprovedMail;
use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class bookingsController extends Controller
{
    public function listBookings(Request $request)
    {
        $this->authorize('viewAny', Bookings::class);
        $validated = $request->validate([
            'filter' => ['sometimes', 'in:active,due,all'],
        ]);
        $selectedCustomer = null;
        $user = $request->user();

        if ($user->role === 'admin' && $request->filled('customer_id')) {
            $selectedCustomer = User::where('role', 'user')->findOrFail($request->query('customer_id'));
        }

        $filter = $validated['filter'] ?? 'active';

        $bookingsQuery = Bookings::with(['customer', 'car']);
        if ($user->role !== 'admin') {
            $bookingsQuery->where('customer_id', $user->id);
        }

        if ($filter === 'active') {
            // Default: show open bookings (pending, confirmed, active)
            $bookingsQuery->whereIn('status', Bookings::OPEN_STATUSES);
        } elseif ($filter === 'due') {
            // A date-only return deadline expires at the end of that day.
            $bookingsQuery->whereIn('status', Bookings::RETURNABLE_STATUSES)
                ->where(function ($q) {
                    $q->whereNotNull('return_requested_at')
                        ->orWhereDate('end_date', '<', today());
                });
        }

        $bookings = $bookingsQuery->latest('id')->get();

        // Calculate counts for tab indicators
        $countsQuery = Bookings::query();
        if ($user->role !== 'admin') {
            $countsQuery->where('customer_id', $user->id);
        }

        $activeCount = (clone $countsQuery)->whereIn('status', Bookings::OPEN_STATUSES)->count();
        $dueCount = (clone $countsQuery)->whereIn('status', Bookings::RETURNABLE_STATUSES)
            ->where(function ($q) {
                $q->whereNotNull('return_requested_at')
                    ->orWhereDate('end_date', '<', today());
            })->count();
        $historyCount = (clone $countsQuery)->where('status', 'completed')->count();

        $carsQuery = Car::orderBy('brand');
        if ($user->role !== 'admin') {
            $carsQuery->where('status', 'available');
        }
        $cars = $carsQuery->get();

        $customersQuery = User::where('role', 'user')->orderBy('name');
        if ($user->role !== 'admin') {
            $customersQuery->whereKey($user->id);
        }
        $customers = $customersQuery->get();

        return view('bookings.list', compact(
            'bookings',
            'cars',
            'customers',
            'selectedCustomer',
            'filter',
            'activeCount',
            'dueCount',
            'historyCount'
        ));
    }

    public function history(Request $request)
    {
        $this->authorize('viewAny', Bookings::class);
        $validated = $request->validate([
            'month' => ['sometimes', 'regex:/^(all|[0-9]{4}-(0[1-9]|1[0-2]))$/'],
        ]);
        $user = $request->user();

        $selectedMonth = $validated['month'] ?? now()->format('Y-m');

        $historyQuery = Bookings::with(['customer', 'car'])
            ->where('status', 'completed');

        if ($user->role !== 'admin') {
            $historyQuery->where('customer_id', $user->id);
        }

        if ($selectedMonth !== 'all') {
            $startOfMonth = Carbon::createFromFormat('!Y-m', $selectedMonth)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $historyQuery->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('returned_at', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($inner) use ($startOfMonth, $endOfMonth) {
                        $inner->whereNull('returned_at')
                            ->whereBetween('end_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
                    });
            });
        }

        $completedBookings = $historyQuery->latest('returned_at')->latest('id')->get();

        $totalCompleted = $completedBookings->count();
        $totalRevenue = $completedBookings->where('payment_status', 'paid')->sum('total_price');

        // Available months for dropdown filter
        $availableMonthsQuery = Bookings::where('status', 'completed');
        if ($user->role !== 'admin') {
            $availableMonthsQuery->where('customer_id', $user->id);
        }
        $availableMonths = $availableMonthsQuery->get(['returned_at', 'end_date'])
            ->map(function ($b) {
                $date = Carbon::parse($b->returned_at ?? $b->end_date);

                return [
                    'value' => $date->format('Y-m'),
                    'label' => $date->format('F Y'),
                ];
            })
            ->unique('value')
            ->values();

        $hasCurrent = $availableMonths->contains(fn ($item) => $item['value'] === now()->format('Y-m'));
        if (! $hasCurrent) {
            $availableMonths->prepend([
                'value' => now()->format('Y-m'),
                'label' => now()->format('F Y'),
            ]);
        }

        return view('bookings.history', compact(
            'completedBookings',
            'selectedMonth',
            'totalCompleted',
            'totalRevenue',
            'availableMonths'
        ));
    }

    public function requestReturn(Request $request, Bookings $booking)
    {
        DB::transaction(function () use ($booking) {
            $booking = Bookings::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $this->authorize('requestReturn', $booking);

            $booking->update(['return_requested_at' => now()]);
        });

        return redirect()->back()
            ->with('success', 'Return request submitted successfully. The vehicle return is awaiting admin confirmation.');
    }

    public function confirmReturn(Request $request, Bookings $booking)
    {
        $validated = $request->validate([
            'payment_status' => ['nullable', 'in:unpaid,paid,refunded'],
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Bookings::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $this->authorize('confirmReturn', $booking);
            $carId = $booking->car_id;

            $updateData = [
                'status' => 'completed',
                'returned_at' => now(),
            ];

            if (! empty($validated['payment_status'])) {
                $updateData['payment_status'] = $validated['payment_status'];
            }

            if (isset($validated['return_notes'])) {
                $updateData['return_notes'] = $validated['return_notes'];
            }

            $booking->update($updateData);

            $this->syncCarAvailability($carId);
        });

        return redirect()->route('show_booking_list')
            ->with('success', "Booking #{$booking->id} confirmed returned! The vehicle is now marked available, and the booking has been moved to Monthly History.");
    }

    public function approve(Request $request, Bookings $booking)
    {
        $booking = DB::transaction(function () use ($booking) {
            $booking = Bookings::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $this->authorize('update', $booking);

            if ($booking->status === 'pending') {
                $booking->update(['status' => 'confirmed']);
            } elseif ($booking->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'booking' => 'Only pending bookings can be approved.',
                ]);
            }

            return $booking->fresh(['car', 'customer']);
        });

        if (! $booking->customer_registered) {
            return redirect()->route('show_booking_list')
                ->with('success', "Booking {$booking->reference} approved. The guest did not have a registered customer account, so no customer email was sent.");
        }

        if ($booking->email_sent_at) {
            return redirect()->route('show_booking_list')
                ->with('success', "Booking {$booking->reference} is approved and its customer confirmation email was already sent.");
        }

        try {
            Mail::to($booking->customer->email)->send(new BookingApprovedMail($booking));
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('show_booking_list')
                ->with('error', "Booking {$booking->reference} was approved, but its customer email could not be sent. Use the approval action again to retry.");
        }

        $booking->update(['email_sent_at' => now()]);

        return redirect()->route('show_booking_list')
            ->with('success', "Booking {$booking->reference} approved and confirmation email sent to the registered customer.");
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
            $customerWasRegistered = false;
            if ($isAdmin) {
                if (! empty($validated['customer_id'])) {
                    $customer = User::where('role', 'user')->findOrFail($validated['customer_id']);
                    $customerWasRegistered = true;
                } else {
                    $customer = User::firstOrNew(['email' => $validated['customer_email']]);
                    if ($customer->exists && $customer->getAttribute('role') !== 'user') {
                        throw ValidationException::withMessages([
                            'customer_email' => 'This email is already associated with a non-customer account.',
                        ]);
                    }
                    $customerWasRegistered = $customer->exists;

                    $customerAttributes = [
                        'name' => $validated['customer_name'],
                        'phone' => $validated['customer_phone'],
                        'address' => $validated['customer_address'] ?? null,
                    ];
                    if (! $customer->exists) {
                        $customerAttributes['password'] = Str::random(64);
                        $customerAttributes['role'] = 'user';
                    }
                    $customer->fill($customerAttributes)->save();
                }
            } else {
                $customer = $request->user();
                $customerWasRegistered = true;
            }

            $days = Carbon::parse($validated['start_date'])
                ->diffInDays(Carbon::parse($validated['end_date']));

            Bookings::create([
                'car_id' => $car->id,
                'customer_id' => $customer->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_price' => $days * $car->daily_rate,
                'customer_registered' => $customerWasRegistered,
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

        $statusRules = $isAdmin
            ? ['required', 'in:pending,confirmed,active,cancelled']
            : ['prohibited'];
        $paymentStatusRules = $isAdmin
            ? ['required', 'in:unpaid,paid,refunded']
            : ['prohibited'];
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
            'status' => $statusRules,
            'payment_status' => $paymentStatusRules,
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
            ->whereIn('status', Bookings::OPEN_STATUSES)
            ->exists();

        $car->update(['status' => $hasOpenBooking ? 'rented' : 'available']);
    }
}
