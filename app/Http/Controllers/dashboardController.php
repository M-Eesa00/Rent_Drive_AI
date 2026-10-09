<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Car;
use App\Models\Customer;

class dashboardController extends Controller
{
    public function index()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $bookingStatusCounts = Bookings::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $carStatusCounts = Car::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalBookings = Bookings::count();
        $totalCars = Car::count();

        $metrics = [
            'totalBookings' => $totalBookings,
            'activeBookings' => $bookingStatusCounts->get('active', 0),
            'availableCars' => $carStatusCounts->get('available', 0),
            'totalCars' => $totalCars,
            'totalCustomers' => Customer::count(),
            'paidRevenue' => Bookings::where('payment_status', 'paid')->sum('total_price'),
        ];

        $monthlyReport = [
            'bookings' => Bookings::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'revenue' => Bookings::where('payment_status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_price'),
        ];

        $recentBookings = Bookings::with(['customer', 'car'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'metrics',
            'bookingStatusCounts',
            'carStatusCounts',
            'monthlyReport',
            'recentBookings'
        ));
    }
}
