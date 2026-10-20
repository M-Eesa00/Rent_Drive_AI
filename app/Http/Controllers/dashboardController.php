<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            $userBookings = $user->bookings()
                ->with('car')
                ->latest('start_date')
                ->latest('id')
                ->get();

            $nextBooking = $user->bookings()
                ->with('car')
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('start_date', '>=', today())
                ->orderBy('start_date')
                ->first();

            $customerMetrics = [
                'totalBookings' => $userBookings->count(),
                'activeBookings' => $userBookings->where('status', 'active')->count(),
                'upcomingBookings' => $userBookings->whereIn('status', ['pending', 'confirmed'])
                    ->filter(fn (Bookings $booking) => $booking->start_date >= today()->toDateString())
                    ->count(),
                'paidTotal' => $userBookings->where('payment_status', 'paid')->sum('total_price'),
            ];

            return view('customers.dashboard', compact('user', 'userBookings', 'nextBooking', 'customerMetrics'));
        }

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
            'totalCustomers' => User::where('role', 'user')->count(),
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
