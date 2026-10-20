@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <a href="{{ route('customers.index') }}" class="text-decoration-none small">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Back to customers
            </a>
            <h1 class="h2 mt-2 mb-1">Customer profile</h1>
            <p class="text-muted mb-0">Customer information and complete booking history.</p>
        </div>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-pen-to-square me-1" aria-hidden="true"></i> Edit customer
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <section class="card border-0 shadow-sm h-100" aria-labelledby="customerDetailsHeading">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if ($customer->image_path)
                        <img src="{{ asset('storage/' . $customer->image_path) }}" alt="{{ $customer->name }}" class="rounded-circle" width="72" height="72" style="object-fit: cover;">
                        @else
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-light text-secondary" style="width: 72px; height: 72px;" aria-hidden="true">
                            <i class="fa-solid fa-user fa-lg"></i>
                        </div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="h5 mb-1 text-break" id="customerDetailsHeading">{{ $customer->name }}</h2>
                            <span class="badge text-bg-light">Customer</span>
                        </div>
                    </div>

                    <dl class="mb-0">
                        <dt class="small text-muted">Email</dt>
                        <dd class="mb-3 text-break">{{ $customer->email }}</dd>
                        <dt class="small text-muted">Phone</dt>
                        <dd class="mb-3">{{ $customer->phone ?: 'Not provided' }}</dd>
                        <dt class="small text-muted">Address</dt>
                        <dd class="mb-3">{{ $customer->address ?: 'Not provided' }}</dd>
                        <dt class="small text-muted">Customer since</dt>
                        <dd class="mb-0">{{ $customer->created_at?->format('M j, Y') ?? '—' }}</dd>
                    </dl>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6">
                    <section class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="small text-muted mb-1">Total bookings</p>
                            <p class="h3 mb-0">{{ $customer->bookings->count() }}</p>
                        </div>
                    </section>
                </div>
                <div class="col-12 col-sm-6">
                    <section class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="small text-muted mb-1">Booking value</p>
                            <p class="h3 mb-0">Rs{{ number_format((float) $customer->bookings->sum('total_price'), 2) }}</p>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card border-0 shadow-sm" aria-labelledby="bookingHistoryHeading">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-1" id="bookingHistoryHeading">Booking history</h2>
                    <p class="small text-muted mb-0">All bookings associated with this customer.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4">Booking</th>
                                    <th scope="col">Vehicle</th>
                                    <th scope="col">Rental period</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="pe-4">Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customer->bookings as $booking)
                                <tr>
                                    <td class="ps-4">#{{ $booking->id }}</td>
                                    <td>{{ $booking->car ? $booking->car->brand . ' ' . $booking->car->model : 'Car removed' }}</td>
                                    <td class="text-nowrap">
                                        {{ \Illuminate\Support\Carbon::parse($booking->start_date)->format('M j, Y') }}
                                        <span class="text-muted">to</span>
                                        {{ \Illuminate\Support\Carbon::parse($booking->end_date)->format('M j, Y') }}
                                    </td>
                                    <td class="text-nowrap">Rs{{ number_format((float) $booking->total_price, 2) }}</td>
                                    <td><span class="badge text-bg-secondary">{{ ucfirst($booking->status) }}</span></td>
                                    <td class="pe-4"><span class="badge {{ $booking->payment_status === 'paid' ? 'text-bg-success' : 'text-bg-light' }}">{{ ucfirst($booking->payment_status) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">This customer has no booking history yet.</td>
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
