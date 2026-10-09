@extends('layouts.main')

@section('content')
@php
$bookingStatuses = [
'pending' => 'Pending',
'confirmed' => 'Confirmed',
'active' => 'Active',
'completed' => 'Completed',
'cancelled' => 'Cancelled',
];
$carStatuses = [
'available' => 'Available',
'rented' => 'Rented',
'maintenance' => 'In maintenance',
];
$bookingTotalForChart = max(1, $bookingStatusCounts->sum());
$carTotalForChart = max(1, $carStatusCounts->sum());
@endphp

<main class="dashboard-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="section-label mb-2">Car rental management</p>
            <h1 class="h2 fw-bold mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">Your operations at a glance.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('show_booking_list') }}" class="btn btn-primary">
                <i class="fa-solid fa-calendar-check me-2" aria-hidden="true"></i>Manage bookings
            </a>
            <a href="{{ route('cars.cars_list') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-car-side me-2" aria-hidden="true"></i>View fleet
            </a>
        </div>
    </div>

    <section aria-labelledby="kpi-heading" class="mb-4">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <p class="section-label mb-1">Performance overview</p>
                <h2 id="kpi-heading" class="h5 fw-bold mb-0">Key performance indicators</h2>
            </div>
            <span class="small text-secondary">{{ now()->format('F Y') }}</span>
        </div>

        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="kpi-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-secondary small mb-2">Active bookings</p>
                            <p class="h3 fw-bold mb-1">{{ number_format($metrics['activeBookings']) }}</p>
                            <p class="small text-secondary mb-0">of {{ number_format($metrics['totalBookings']) }} total bookings</p>
                        </div>
                        <span class="kpi-icon"><i class="fa-solid fa-key" aria-hidden="true"></i></span>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="kpi-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-secondary small mb-2">Available vehicles</p>
                            <p class="h3 fw-bold mb-1">{{ number_format($metrics['availableCars']) }}</p>
                            <p class="small text-secondary mb-0">of {{ number_format($metrics['totalCars']) }} vehicles in fleet</p>
                        </div>
                        <span class="kpi-icon"><i class="fa-solid fa-car" aria-hidden="true"></i></span>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="kpi-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-secondary small mb-2">Registered customers</p>
                            <p class="h3 fw-bold mb-1">{{ number_format($metrics['totalCustomers']) }}</p>
                            <p class="small text-secondary mb-0">customer accounts</p>
                        </div>
                        <span class="kpi-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></span>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="kpi-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-secondary small mb-2">Paid revenue</p>
                            <p class="h3 fw-bold mb-1">Rs {{ number_format($metrics['paidRevenue'], 2) }}</p>
                            <p class="small text-secondary mb-0">from paid bookings</p>
                        </div>
                        <span class="kpi-icon"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <section class="col-12 col-lg-7" aria-labelledby="analytics-heading">
            <div class="dashboard-panel h-100 p-4">
                <div class="mb-4">
                    <p class="section-label mb-1">Analytics</p>
                    <h2 id="analytics-heading" class="h5 fw-bold mb-0">Bookings and fleet status</h2>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-semibold mb-0">Booking status</h3>
                        <span class="small text-secondary">{{ number_format($bookingStatusCounts->sum()) }} total</span>
                    </div>
                    @foreach ($bookingStatuses as $status => $label)
                    @php
                    $count = (int) $bookingStatusCounts->get($status, 0);
                    $percentage = (int) round(($count / $bookingTotalForChart) * 100);
                    $progressClass = 'bg-primary';
                    if ($status === 'cancelled') {
                    $progressClass = 'bg-secondary';
                    } elseif ($status === 'active') {
                    $progressClass = 'bg-success';
                    }
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $label }}</span>
                            <span class="text-secondary">{{ number_format($count) }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="progress" role="progressbar" aria-label="{{ $label }} bookings" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.5rem;">
                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="my-4">

                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-semibold mb-0">Fleet availability</h3>
                        <span class="small text-secondary">{{ number_format($carStatusCounts->sum()) }} vehicles</span>
                    </div>
                    <div class="row g-3">
                        @foreach ($carStatuses as $status => $label)
                        @php
                        $count = (int) $carStatusCounts->get($status, 0);
                        $percentage = (int) round(($count / $carTotalForChart) * 100);
                        $progressClass = 'bg-primary';
                        if ($status === 'available') {
                        $progressClass = 'bg-success';
                        } elseif ($status === 'maintenance') {
                        $progressClass = 'bg-warning';
                        }
                        @endphp
                        <div class="col-12 col-sm-4">
                            <div class="d-flex justify-content-between small mb-2">
                                <span>{{ $label }}</span>
                                <span class="fw-semibold">{{ number_format($count) }}</span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="{{ $label }} vehicles" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.5rem;">
                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="col-12 col-lg-5" aria-labelledby="reports-heading">
            <div class="dashboard-panel h-100 p-4">
                <div class="mb-4">
                    <p class="section-label mb-1">Reports</p>
                    <h2 id="reports-heading" class="h5 fw-bold mb-0">Monthly snapshot</h2>
                </div>

                <div class="rounded-3 p-3 mb-3" style="background: #f4f7fb;">
                    <p class="small text-secondary mb-1">Bookings created this month</p>
                    <p class="h3 fw-bold mb-0">{{ number_format($monthlyReport['bookings']) }}</p>
                </div>
                <div class="rounded-3 p-3 mb-4" style="background: #f4f7fb;">
                    <p class="small text-secondary mb-1">Paid revenue this month</p>
                    <p class="h3 fw-bold mb-0">Rs {{ number_format($monthlyReport['revenue'], 2) }}</p>
                </div>

                <h3 class="h6 fw-semibold mb-3">Quick navigation</h3>
                <div class="d-grid gap-2">
                    <a href="{{ route('show_booking_list') }}" class="btn btn-light border text-start py-2">
                        <i class="fa-solid fa-file-lines text-primary me-2" aria-hidden="true"></i>Booking report
                        <i class="fa-solid fa-arrow-right float-end mt-1 text-secondary" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('cars.cars_list') }}" class="btn btn-light border text-start py-2">
                        <i class="fa-solid fa-car-side text-primary me-2" aria-hidden="true"></i>Fleet inventory
                        <i class="fa-solid fa-arrow-right float-end mt-1 text-secondary" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-light border text-start py-2">
                        <i class="fa-solid fa-address-book text-primary me-2" aria-hidden="true"></i>Customer directory
                        <i class="fa-solid fa-arrow-right float-end mt-1 text-secondary" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>
    </div>

    <section class="dashboard-panel p-4" aria-labelledby="recent-bookings-heading">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <p class="section-label mb-1">Reports</p>
                <h2 id="recent-bookings-heading" class="h5 fw-bold mb-0">Recent bookings</h2>
            </div>
            <a href="{{ route('show_booking_list') }}" class="btn btn-sm btn-outline-primary">View all bookings</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Customer</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Rental period</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentBookings as $booking)
                    <tr>
                        <td class="fw-semibold">{{ $booking->customer?->name ?? 'Customer removed' }}</td>
                        <td>{{ $booking->car ? $booking->car->brand . ' ' . $booking->car->model : 'Car removed' }}</td>
                        <td class="text-nowrap">{{ $booking->start_date }} – {{ $booking->end_date }}</td>
                        <td class="text-nowrap">Rs {{ number_format($booking->total_price, 2) }}</td>
                        <td><span class="badge rounded-pill text-bg-light border">{{ ucfirst($booking->status) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">No bookings to report yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection