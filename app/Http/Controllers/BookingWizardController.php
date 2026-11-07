<?php

namespace App\Http\Controllers;

use App\Mail\BookingRequestMail;
use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingWizardController extends Controller
{
    public function create(Request $request)
    {
        return view('booking.create', [
            'customer' => $request->user(),
        ]);
    }

    public function availability(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $cars = Car::query()
            ->whereIn('status', ['available', 'rented'])
            ->whereDoesntHave('bookings', function ($query) use ($validated) {
                $query->reservesDates()
                    ->whereDate('start_date', '<=', $validated['end_date'])
                    ->whereDate('end_date', '>=', $validated['start_date']);
            })
            ->orderBy('brand')
            ->orderBy('model')
            ->get(['id', 'brand', 'model', 'year', 'plate_number', 'daily_rate', 'gear_type', 'fuel_type', 'seats', 'image'])
            ->map(function (Car $car) use ($validated) {
                $days = max(1, (int) Carbon::parse($validated['start_date'])
                    ->diffInDays(Carbon::parse($validated['end_date'])));

                return [
                    'id' => $car->id,
                    'name' => trim($car->brand.' '.$car->model),
                    'year' => $car->year,
                    'plate_number' => $car->plate_number,
                    'daily_rate' => (float) $car->daily_rate,
                    'total_price' => round((float) $car->daily_rate * $days, 2),
                    'gear_type' => $car->gear_type,
                    'fuel_type' => $car->fuel_type,
                    'seats' => $car->seats,
                    'image' => $car->image ? asset('storage/'.$car->image) : null,
                ];
            })
            ->values();

        return response()->json(['cars' => $cars]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'booking_acknowledged' => ['accepted'],
        ]);

        $authenticatedCustomer = $request->user()?->role === 'user' ? $request->user() : null;
        if ($authenticatedCustomer && strcasecmp($authenticatedCustomer->email, $validated['customer_email']) !== 0) {
            throw ValidationException::withMessages([
                'customer_email' => 'Use the email address associated with your customer account.',
            ]);
        }

        $booking = DB::transaction(function () use ($validated, $authenticatedCustomer) {
            $car = Car::whereKey($validated['car_id'])
                ->whereIn('status', ['available', 'rented'])
                ->lockForUpdate()
                ->first();

            if (! $car) {
                throw ValidationException::withMessages([
                    'car_id' => 'This vehicle is not available for the selected dates.',
                ]);
            }

            $hasConflict = Bookings::query()
                ->where('car_id', $car->id)
                ->reservesDates()
                ->whereDate('start_date', '<=', $validated['end_date'])
                ->whereDate('end_date', '>=', $validated['start_date'])
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'car_id' => 'This vehicle was just reserved for those dates. Please choose another vehicle or date range.',
                ]);
            }

            $customer = $authenticatedCustomer ?? User::where('email', $validated['customer_email'])->first();
            if ($customer && $customer->role !== 'user') {
                throw ValidationException::withMessages([
                    'customer_email' => 'This email is already associated with a staff account. Use a customer email address.',
                ]);
            }

            $customerWasRegistered = $customer !== null;
            if (! $customer) {
                $customer = User::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'password' => Str::random(64),
                    'role' => 'user',
                    'phone' => $validated['customer_phone'],
                ]);
            }

            $days = max(1, (int) Carbon::parse($validated['start_date'])
                ->diffInDays(Carbon::parse($validated['end_date'])));
            $reference = 'RD-'.now()->format('Y').'-'.Str::upper((string) Str::ulid());

            $booking = Bookings::create([
                'car_id' => $car->id,
                'customer_id' => $customer->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_price' => $days * $car->daily_rate,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'reference' => $reference,
                'customer_registered' => $customerWasRegistered,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            $car->update(['status' => 'rented']);

            return $booking->load(['car', 'customer']);
        });

        try {
            $adminEmail = config('services.booking.admin_email') ?: config('mail.from.address');
            if (! filled($adminEmail)) {
                throw new \RuntimeException('No booking-admin email address is configured.');
            }
            Mail::to($adminEmail)->send(new BookingRequestMail($booking));
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('booking.confirmation', $booking->reference)
                ->with('warning', 'Your request was saved, but the admin email notification could not be sent. Please contact the rental team and provide your booking reference.');
        }

        return redirect()->route('booking.confirmation', $booking->reference)
            ->with('success', 'Your booking request has been submitted and is waiting for admin approval.');
    }

    public function confirmation(string $reference)
    {
        $booking = Bookings::with(['car', 'customer'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('booking.confirmation', compact('booking'));
    }
}
