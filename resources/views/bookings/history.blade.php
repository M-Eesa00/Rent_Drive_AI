@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('show_booking_list') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Back to Active Bookings
                </a>
            </div>
            <h1 class="h2 fw-bold mt-2 mb-1">Monthly Booking History</h1>
            <p class="text-muted mb-0">Archived records of completed and returned vehicle rentals.</p>
        </div>

        <form method="GET" action="{{ route('bookings.history') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <label for="monthFilter" class="small fw-semibold text-muted text-nowrap">Filter Month:</label>
            <select name="month" id="monthFilter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="all" {{ $selectedMonth === 'all' ? 'selected' : '' }}>All Time</option>
                @foreach ($availableMonths as $monthOption)
                <option value="{{ $monthOption['value'] }}" {{ $selectedMonth === $monthOption['value'] ? 'selected' : '' }}>
                    {{ $monthOption['label'] }}
                </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="btn btn-sm btn-secondary">Apply</button></noscript>
        </form>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Unable to filter booking history.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <section class="row g-3 mb-4" aria-labelledby="history-metrics-heading">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Completed Rentals</p>
                            <h3 class="fw-bold mb-0 text-success">{{ number_format($totalCompleted) }}</h3>
                            <p class="small text-muted mb-0">Cars successfully returned</p>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                            <i class="fa-solid fa-circle-check fa-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Revenue Realized</p>
                            <h3 class="fw-bold mb-0 text-primary">Rs {{ number_format($totalRevenue, 2) }}</h3>
                            <p class="small text-muted mb-0">From paid completed rentals</p>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <i class="fa-solid fa-wallet fa-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Viewing Period</p>
                            <h3 class="h4 fw-bold mb-0 text-dark">
                                @if ($selectedMonth === 'all')
                                All Months
                                @else
                                {{ \Carbon\Carbon::createFromFormat('!Y-m', $selectedMonth)->format('F Y') }}
                                @endif
                            </h3>
                            <p class="small text-muted mb-0">Filtered archive records</p>
                        </div>
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-3">
                            <i class="fa-solid fa-calendar-days fa-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h2 class="h5 fw-bold mb-0">Archived Booking Records</h2>
            <span class="badge text-bg-light border">{{ $completedBookings->count() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-nowrap">Ref #</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Rental Period</th>
                        <th scope="col">Returned At</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Payment</th>
                        <th scope="col">Return Notes</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($completedBookings as $booking)
                    <tr>
                        <td class="text-nowrap fw-semibold">#{{ $booking->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $booking->customer?->name ?? 'Customer removed' }}</div>
                            @if ($booking->customer?->phone)
                            <div class="small text-muted">{{ $booking->customer->phone }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($booking->car)
                            <span class="fw-semibold">{{ $booking->car->brand }} {{ $booking->car->model }}</span>
                            <span class="badge text-bg-secondary ms-1">{{ $booking->car->plate_number }}</span>
                            @else
                            <span class="text-muted">Car removed</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <span class="small">{{ $booking->start_date }}</span>
                            <span class="text-muted small">to</span>
                            <span class="small">{{ $booking->end_date }}</span>
                        </td>
                        <td class="text-nowrap">
                            @if ($booking->returned_at)
                            <span class="badge text-bg-success">
                                <i class="fa-solid fa-check me-1" aria-hidden="true"></i>{{ $booking->returned_at->format('M j, Y H:i') }}
                            </span>
                            @else
                            <span class="badge text-bg-success">Returned</span>
                            @endif
                        </td>
                        <td class="text-nowrap fw-semibold">Rs {{ number_format($booking->total_price, 2) }}</td>
                        <td>
                            <span class="badge {{ $booking->payment_status === 'paid' ? 'text-bg-success' : 'text-bg-warning' }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td class="small text-muted" style="max-width: 200px;">
                            {{ $booking->return_notes ?: '—' }}
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-eye me-1" aria-hidden="true"></i>Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fa-solid fa-box-open fa-2x mb-3 text-secondary d-block" aria-hidden="true"></i>
                            No completed rentals found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
