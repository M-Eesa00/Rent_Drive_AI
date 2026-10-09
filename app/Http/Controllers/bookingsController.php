<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Car;
use App\Models\Customer;
use Carbon\Carbon;

class bookingsController extends Controller
{
    public function listBookings()
    {
        $bookings = Bookings::with(['customer', 'car'])->get();
        $cars = Car::orderBy('brand')->get();
        $customers = Customer::orderBy('name')->get();

        return view("bookings.list", compact('bookings', 'cars', 'customers'));
    }

    public function edit(Bookings $booking)
    {
        $cars = Car::orderBy('brand')->get();
        $customers = Customer::orderBy('name')->get();

        return view('bookings.edit', compact('booking', 'cars', 'customers'));
    }

    public function index()
    {
        return response()->json(Bookings::with(['customer', 'car'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $car = Car::findOrFail($validated['car_id']);
        $customer = Customer::updateOrCreate(
            ['email' => $validated['customer_email']],
            [
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'],
                'address' => $validated['customer_address'] ?? null,
            ]
        );
        $days = Carbon::parse($validated['start_date'])
            ->diffInDays(Carbon::parse($validated['end_date']));

        Bookings::create([
            'car_id' => $car->id,
            'customer_id' => $customer->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $days * $car->daily_rate,
        ]);

        return redirect()->route('show_booking_list')
            ->with('success', 'Booking created successfully.');
    }

    public function update(Request $request, Bookings $booking)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:pending,confirmed,active,completed,cancelled'],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
        ]);

        $car = Car::findOrFail($validated['car_id']);
        $days = Carbon::parse($validated['start_date'])
            ->diffInDays(Carbon::parse($validated['end_date']));
        $validated['total_price'] = $days * $car->daily_rate;

        $booking->update($validated);

        return redirect()->route('show_booking_list')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Bookings $booking)
    {
        $booking->delete();

        return redirect()->route('show_booking_list')->with('success', 'Booking deleted successfully.');
    }
}
