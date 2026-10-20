@extends('layouts.main')

@section('content')
<main class="py-4">
    <section class="rounded-4 p-4 p-lg-5 mb-4 text-white" style="background: linear-gradient(120deg, #172b4d, #315b8a);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
            <div>
                <p class="small text-uppercase fw-semibold mb-2 opacity-75">Your rental account</p>
                <div class="d-flex align-items-center gap-3">
                    @if ($user->image_path)
                    <img src="{{ asset('storage/' . $user->image_path) }}" alt="" class="rounded-circle border border-2 border-white" width="64" height="64" style="object-fit: cover;">
                    @else
                    <span class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25" style="width: 64px; height: 64px;" aria-hidden="true">
                        <i class="fa-solid fa-user fa-lg"></i>
                    </span>
                    @endif
                    <h1 class="display-6 fw-bold mb-0">Welcome, {{ $user->name }}</h1>
                </div>
                <p class="mb-0 opacity-75 mt-2">Your bookings, account details, and rental activity in one place.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('show_booking_list') }}" class="btn btn-light">
                    <i class="fa-solid fa-calendar-check me-2" aria-hidden="true"></i>My bookings
                </a>
                <a href="{{ route('cars.cars_list') }}" class="btn btn-outline-light">
                    <i class="fa-solid fa-car-side me-2" aria-hidden="true"></i>Browse cars
                </a>
            </div>
        </div>
    </section>

    <section class="mb-4" aria-labelledby="customerOverviewHeading">
        <div class="mb-3">
            <p class="small text-uppercase text-secondary fw-semibold mb-1">Overview</p>
            <h2 class="h5 fw-bold mb-0" id="customerOverviewHeading">Your rental activity</h2>
        </div>
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="small text-muted mb-2">Total bookings</p>
                        <p class="h3 fw-bold mb-0">{{ number_format($customerMetrics['totalBookings']) }}</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="small text-muted mb-2">Upcoming bookings</p>
                        <p class="h3 fw-bold mb-0">{{ number_format($customerMetrics['upcomingBookings']) }}</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="small text-muted mb-2">Active rentals</p>
                        <p class="h3 fw-bold mb-0">{{ number_format($customerMetrics['activeBookings']) }}</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="small text-muted mb-2">Paid booking total</p>
                        <p class="h3 fw-bold mb-0">Rs {{ number_format((float) $customerMetrics['paidTotal'], 2) }}</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <section class="card border-0 shadow-sm h-100" aria-labelledby="accountDetailsHeading">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-4" id="accountDetailsHeading">Account details</h2>
                    <dl class="mb-0">
                        <dt class="small text-muted">Full name</dt>
                        <dd class="mb-3">{{ $user->name }}</dd>
                        <dt class="small text-muted">Email address</dt>
                        <dd class="mb-3 text-break">{{ $user->email }}</dd>
                        <dt class="small text-muted">Phone</dt>
                        <dd class="mb-3">{{ $user->phone ?: 'Not provided' }}</dd>
                        <dt class="small text-muted">Address</dt>
                        <dd class="mb-3">{{ $user->address ?: 'Not provided' }}</dd>
                        <dt class="small text-muted">Member since</dt>
                        <dd class="mb-0">{{ $user->created_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-8">
            @if ($nextBooking)
            <section class="card border-0 shadow-sm mb-4" aria-labelledby="nextBookingHeading">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <p class="small text-uppercase text-primary fw-semibold mb-1">Coming up</p>
                            <h2 class="h5 fw-bold mb-0" id="nextBookingHeading">Your next booking</h2>
                        </div>
                        <span class="badge text-bg-light">{{ ucfirst($nextBooking->status) }}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <p class="small text-muted mb-1">Vehicle</p>
                            <p class="fw-semibold mb-0">{{ $nextBooking->car ? $nextBooking->car->brand . ' ' . $nextBooking->car->model : 'Vehicle details unavailable' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p class="small text-muted mb-1">Rental period</p>
                            <p class="fw-semibold mb-0">
                                {{ \Illuminate\Support\Carbon::parse($nextBooking->start_date)->format('M j, Y') }}
                                <span class="text-muted fw-normal">to</span>
                                {{ \Illuminate\Support\Carbon::parse($nextBooking->end_date)->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="col-12">
                            <p class="small text-muted mb-1">Booking total</p>
                            <p class="fw-semibold mb-0">Rs {{ number_format((float) $nextBooking->total_price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <section class="card border-0 shadow-sm" aria-labelledby="recentBookingsHeading">
                <div class="card-header bg-white border-0 p-4 pb-2 d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <p class="small text-uppercase text-secondary fw-semibold mb-1">Your activity</p>
                        <h2 class="h5 fw-bold mb-0" id="recentBookingsHeading">Recent bookings</h2>
                    </div>
                    <a href="{{ route('show_booking_list') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4">Vehicle</th>
                                    <th scope="col">Rental period</th>
                                    <th scope="col">Total</th>
                                    <th scope="col" class="pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userBookings->take(5) as $booking)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $booking->car ? $booking->car->brand . ' ' . $booking->car->model : 'Vehicle details unavailable' }}</td>
                                    <td class="text-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($booking->start_date)->format('M j, Y') }}
                                        <span class="text-muted">to</span>
                                        {{ \Illuminate\Support\Carbon::parse($booking->end_date)->format('M j, Y') }}
                                    </td>
                                    <td class="text-nowrap">Rs {{ number_format((float) $booking->total_price, 2) }}</td>
                                    <td class="pe-4"><span class="badge text-bg-light border">{{ ucfirst($booking->status) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        You don't have any bookings yet.
                                        <a href="{{ route('cars.cars_list') }}" class="d-block mt-2">Explore available cars</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
@endsection