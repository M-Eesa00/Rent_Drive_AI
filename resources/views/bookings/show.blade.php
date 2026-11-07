@extends('layouts.main')

@section('content')
@php
    $startDate = \Illuminate\Support\Carbon::parse($booking->start_date);
    $endDate = \Illuminate\Support\Carbon::parse($booking->end_date);
    $rentalDays = max(1, $startDate->diffInDays($endDate));
@endphp

<main class="py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a href="{{ route('show_booking_list') }}" class="small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Back to bookings
            </a>
            <h1 class="h2 fw-bold mt-2 mb-1">Booking details</h1>
            <p class="text-muted mb-0">Reference {{ $booking->reference ?: '#'.$booking->id }} · Created {{ $booking->created_at?->format('M j, Y') ?? '—' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-pen-to-square me-1" aria-hidden="true"></i> Edit booking
            </a>
        </div>
    </div>

    <section class="card border-0 shadow-sm mb-4" aria-labelledby="bookingStatusHeading">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <p class="small text-uppercase text-muted fw-semibold mb-1">Booking status</p>
                    <h2 class="h4 mb-0" id="bookingStatusHeading">{{ ucfirst($booking->status) }}</h2>
                </div>
                <div class="text-md-end">
                    <p class="small text-muted mb-1">Payment</p>
                    <span class="badge {{ $booking->payment_status === 'paid' ? 'text-bg-success' : ($booking->payment_status === 'refunded' ? 'text-bg-secondary' : 'text-bg-warning') }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <section class="card border-0 shadow-sm h-100" aria-labelledby="vehicleHeading">
                @if ($booking->car?->image)
                <img src="{{ asset('storage/' . $booking->car->image) }}" class="card-img-top" alt="{{ $booking->car->brand }} {{ $booking->car->model }}" style="height: 280px; object-fit: cover;">
                @else
                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 220px;">
                    <i class="fa-solid fa-car-side fa-3x" aria-hidden="true"></i>
                    <span class="visually-hidden">No vehicle photo</span>
                </div>
                @endif
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                        <div>
                            <p class="small text-uppercase text-muted fw-semibold mb-1">Reserved vehicle</p>
                            <h2 class="h4 fw-bold mb-0" id="vehicleHeading">
                                {{ $booking->car ? $booking->car->brand . ' ' . $booking->car->model : 'Vehicle details unavailable' }}
                            </h2>
                        </div>
                        @if ($booking->car)
                        <span class="badge text-bg-light">{{ $booking->car->year }}</span>
                        @endif
                    </div>
                    @if ($booking->car)
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted fw-normal">Registration</dt>
                        <dd class="col-sm-7 fw-semibold">{{ $booking->car->plate_number }}</dd>
                        <dt class="col-sm-5 text-muted fw-normal">Transmission</dt>
                        <dd class="col-sm-7">{{ $booking->car->gear_type }}</dd>
                        <dt class="col-sm-5 text-muted fw-normal">Fuel</dt>
                        <dd class="col-sm-7">{{ $booking->car->fuel_type }}</dd>
                        <dt class="col-sm-5 text-muted fw-normal">Seats</dt>
                        <dd class="col-sm-7 mb-0">{{ $booking->car->seats }}</dd>
                    </dl>
                    @endif
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-5">
            <section class="card border-0 shadow-sm mb-4" aria-labelledby="rentalPeriodHeading">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-muted fw-semibold mb-1">Schedule</p>
                    <h2 class="h5 fw-bold mb-4" id="rentalPeriodHeading">Rental period</h2>
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="small text-muted mb-1">Pick-up</p>
                            <p class="fw-semibold mb-0">{{ $startDate->format('M j, Y') }}</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-muted mb-1">Return</p>
                            <p class="fw-semibold mb-0">{{ $endDate->format('M j, Y') }}</p>
                        </div>
                        <div class="col-12">
                            <hr>
                            <p class="small text-muted mb-1">Duration</p>
                            <p class="fw-semibold mb-0">{{ $rentalDays }} {{ \Illuminate\Support\Str::plural('day', $rentalDays) }}</p>
                        </div>
                    </div>
                </div>
            </section>

            @if ($booking->special_requests)
            <section class="card border-0 shadow-sm mb-4" aria-labelledby="specialRequestsHeading">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-muted fw-semibold mb-1">Customer note</p>
                    <h2 class="h5 fw-bold mb-3" id="specialRequestsHeading">Special requests</h2>
                    <p class="mb-0">{{ $booking->special_requests }}</p>
                </div>
            </section>
            @endif

            <section class="card border-0 shadow-sm mb-4" aria-labelledby="priceSummaryHeading">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-muted fw-semibold mb-1">Payment summary</p>
                    <h2 class="h5 fw-bold mb-4" id="priceSummaryHeading">Booking total</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Daily rate</span>
                        <span>{{ $booking->car ? 'Rs ' . number_format((float) $booking->car->daily_rate, 2) : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Rental duration</span>
                        <span>{{ $rentalDays }} {{ \Illuminate\Support\Str::plural('day', $rentalDays) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Total</span>
                        <span class="h4 fw-bold mb-0">Rs {{ number_format((float) $booking->total_price, 2) }}</span>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Payment status: {{ ucfirst($booking->payment_status) }}</p>
                </div>
            </section>

            @if (auth()->user()->role === 'admin' && $booking->customer)
            <section class="card border-0 shadow-sm" aria-labelledby="customerHeading">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-muted fw-semibold mb-1">Reserved for</p>
                    <h2 class="h5 fw-bold mb-3" id="customerHeading">{{ $booking->customer->name }}</h2>
                    <p class="mb-1"><i class="fa-regular fa-envelope text-muted me-2" aria-hidden="true"></i>{{ $booking->customer->email }}</p>
                    <p class="mb-0"><i class="fa-solid fa-phone text-muted me-2" aria-hidden="true"></i>{{ $booking->customer->phone ?: 'Phone not provided' }}</p>
                </div>
            </section>
            @endif
        </div>
    </div>
</main>
@endsection
