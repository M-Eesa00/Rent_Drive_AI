<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Car;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;

class AdminCalendarController extends Controller
{
    public function events(Request $request)
    {
        $range = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        // FullCalendar's end date is exclusive; booking end dates are inclusive.
        $rangeStart = Carbon::parse($range['start'])->toDateString();
        $rangeEnd = Carbon::parse($range['end'])->toDateString();

        $bookings = Bookings::with(['car', 'customer'])
            ->whereIn('status', [
                ...Bookings::OPEN_STATUSES,
                'completed',
            ])
            ->whereDate('start_date', '<', $rangeEnd)
            ->whereDate('end_date', '>=', $rangeStart)
            ->get();

        $colors = [
            'pending' => '#f0ad4e',
            'confirmed' => '#0dcaf0',
            'active' => '#198754',
            'completed' => '#6c757d',
        ];

        return response()->json($bookings->map(function (Bookings $booking) use ($colors) {
            $carName = $booking->car
                ? $booking->car->brand . ' ' . $booking->car->model
                : 'Car removed';

            return [
                'id' => $booking->id,
                'title' => $carName,
                'start' => Carbon::parse($booking->start_date)->toDateString(),
                'end' => Carbon::parse($booking->end_date)->addDay()->toDateString(),
                'allDay' => true,
                'backgroundColor' => $colors[$booking->status] ?? '#6c757d',
                'extendedProps' => [
                    'status' => $booking->status,
                    'customer' => $booking->customer?->name ?? 'Customer removed',
                    'plate' => $booking->car?->plate_number ?? '',
                ],
            ];
        })->values());
    }

    public function availability(Request $request)
    {
        $dates = $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $cars = Car::query()
            ->whereIn('status', ['available', 'rented'])
            ->whereDoesntHave('bookings', function ($query) use ($dates) {
                $query->reservesDates()
                    ->whereDate('start_date', '<=', $dates['end_date'])
                    ->whereDate('end_date', '>=', $dates['start_date']);
            })
            ->orderBy('brand')
            ->orderBy('model')
            ->get(['id', 'brand', 'model', 'plate_number']);

        return response()->json([
            'cars' => $cars->map(fn(Car $car) => [
                'id' => $car->id,
                'name' => $car->brand . ' ' . $car->model,
                'plate_number' => $car->plate_number,
            ])->values(),
        ]);
    }
}
