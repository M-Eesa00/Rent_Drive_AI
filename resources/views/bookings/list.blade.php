@extends('layouts.main')

@section('content')

<main class="py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h2 fw-bold mb-1">Bookings</h1>
            <p class="text-muted mb-0">Manage customer vehicle reservations and dynamic returns.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('bookings.history') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-clock-rotate-left me-1" aria-hidden="true"></i> Month History ({{ $historyCount }})
            </a>
            @if (auth()->user()->role === 'admin')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add Booking
            </button>
            @else
            <a href="{{ route('booking.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-calendar-plus me-1" aria-hidden="true"></i> Book a vehicle
            </a>
            @endif
        </div>
    </div>

    <!-- Navigation Filter Tabs -->
    <ul class="nav nav-pills mb-3 gap-2">
        <li class="nav-item">
            <a class="nav-link {{ $filter === 'active' ? 'active' : '' }}" href="{{ route('show_booking_list', ['filter' => 'active']) }}">
                Active Rentals <span class="badge text-bg-light ms-1">{{ $activeCount }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $filter === 'due' ? 'active bg-danger' : '' }}" href="{{ route('show_booking_list', ['filter' => 'due']) }}">
                Due / Pending Returns
                @if ($dueCount > 0)
                <span class="badge {{ $filter === 'due' ? 'text-bg-light' : 'text-bg-danger' }} ms-1">{{ $dueCount }}</span>
                @else
                <span class="badge text-bg-light ms-1">0</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('show_booking_list', ['filter' => 'all']) }}">
                All Records
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('bookings.history') }}">
                <i class="fa-solid fa-calendar-check me-1" aria-hidden="true"></i> Monthly History
            </a>
        </li>
    </ul>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Unable to process request.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    @if (auth()->user()->role === 'admin' && $dueCount > 0 && $filter !== 'due')
    <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" role="alert">
        <div>
            <i class="fa-solid fa-triangle-exclamation me-2" aria-hidden="true"></i>
            <strong>Action Needed:</strong> There are {{ $dueCount }} booking(s) awaiting return confirmation or past their scheduled return date.
        </div>
        <a href="{{ route('show_booking_list', ['filter' => 'due']) }}" class="btn btn-sm btn-warning">View Pending Returns</a>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-nowrap">Sr #</th>
                        <th>Customer</th>
                        <th>Car</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                    <tr>
                        <td class="text-nowrap">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $booking->customer?->name ?? 'Customer removed' }}</div>
                            @if ($booking->customer?->phone)
                            <div class="small text-muted">{{ $booking->customer->phone }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($booking->car)
                            <span class="fw-semibold">{{ $booking->car->brand }} {{ $booking->car->model }}</span>
                            <div class="small text-muted">{{ $booking->car->plate_number }}</div>
                            @else
                            <span class="text-muted">Car removed</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $booking->start_date }}</td>
                        <td class="text-nowrap">{{ $booking->end_date }}</td>
                        <td class="text-nowrap fw-semibold">Rs {{ number_format($booking->total_price, 2) }}</td>
                        <td>
                            @if ($booking->status === 'completed')
                                <span class="badge text-bg-success">
                                    <i class="fa-solid fa-circle-check me-1" aria-hidden="true"></i>Completed
                                </span>
                            @elseif ($booking->isReturnRequested())
                                <span class="badge text-bg-warning text-dark">
                                    <i class="fa-solid fa-clock-rotate-left me-1" aria-hidden="true"></i>Return Requested
                                </span>
                            @elseif ($booking->isDueForReturn())
                                <span class="badge text-bg-danger">
                                    <i class="fa-solid fa-triangle-exclamation me-1" aria-hidden="true"></i>Return Due
                                </span>
                            @elseif ($booking->status === 'active')
                                <span class="badge text-bg-primary">
                                    <i class="fa-solid fa-car-side me-1" aria-hidden="true"></i>Active
                                </span>
                            @elseif ($booking->status === 'pending')
                                <span class="badge text-bg-warning text-dark">Pending approval</span>
                            @elseif ($booking->status === 'confirmed')
                                <span class="badge text-bg-info text-dark">Confirmed</span>
                            @elseif ($booking->status === 'cancelled')
                                <span class="badge text-bg-secondary">Cancelled</span>
                            @else
                                <span class="badge text-bg-light border">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $booking->payment_status === 'paid' ? 'text-bg-success' : ($booking->payment_status === 'refunded' ? 'text-bg-secondary' : 'text-bg-warning') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            @if (auth()->user()->role === 'admin' && $booking->status === 'pending')
                                <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this booking request?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-check me-1" aria-hidden="true"></i>Approve booking
                                    </button>
                                </form>
                            @elseif (auth()->user()->role === 'admin' && $booking->status === 'confirmed' && $booking->customer_registered && !$booking->email_sent_at)
                                <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Retry customer email</button>
                                </form>
                            @elseif (auth()->user()->role === 'admin' && $booking->isDueForReturn())
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmReturnModal-{{ $booking->id }}">
                                    <i class="fa-solid fa-clipboard-check me-1" aria-hidden="true"></i>Confirm Return
                                </button>
                            @elseif (auth()->user()->role !== 'admin' && $booking->isOpen() && !$booking->isReturnRequested())
                                <form action="{{ route('bookings.request_return', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you ready to return this vehicle?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning text-dark">
                                        <i class="fa-solid fa-arrow-turn-down me-1" aria-hidden="true"></i>Return Car
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                            @if (auth()->user()->role === 'admin')
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this booking?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    @if (auth()->user()->role === 'admin' && $booking->isDueForReturn())
                    <!-- Admin Confirm Return Modal for this booking -->
                    <div class="modal fade" id="confirmReturnModal-{{ $booking->id }}" tabindex="-1" aria-labelledby="confirmReturnModalLabel-{{ $booking->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content text-start">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fw-bold" id="confirmReturnModalLabel-{{ $booking->id }}">
                                        <i class="fa-solid fa-clipboard-check text-success me-2" aria-hidden="true"></i>Confirm Vehicle Return
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('bookings.confirm_return', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="alert alert-info py-2 small mb-3">
                                            Confirming this return will automatically mark the car as <strong>Available</strong>, delete this booking from the active list, and archive it into the <strong>Month History</strong>.
                                        </div>

                                        <dl class="row small mb-3">
                                            <dt class="col-sm-4 text-muted">Vehicle:</dt>
                                            <dd class="col-sm-8 fw-semibold">{{ $booking->car ? $booking->car->brand . ' ' . $booking->car->model . ' (' . $booking->car->plate_number . ')' : 'Vehicle N/A' }}</dd>

                                            <dt class="col-sm-4 text-muted">Customer:</dt>
                                            <dd class="col-sm-8 fw-semibold">{{ $booking->customer?->name ?? 'Customer N/A' }}</dd>

                                            <dt class="col-sm-4 text-muted">Rental Schedule:</dt>
                                            <dd class="col-sm-8">{{ $booking->start_date }} to {{ $booking->end_date }}</dd>

                                            <dt class="col-sm-4 text-muted">Total Price:</dt>
                                            <dd class="col-sm-8 fw-bold text-primary">Rs {{ number_format($booking->total_price, 2) }}</dd>
                                        </dl>

                                        <div class="mb-3">
                                            <label for="payment_status_{{ $booking->id }}" class="form-label small fw-semibold">Payment Status</label>
                                            <select name="payment_status" id="payment_status_{{ $booking->id }}" class="form-select form-select-sm">
                                                <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>Paid (Settled)</option>
                                                <option value="unpaid" {{ $booking->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                <option value="refunded" {{ $booking->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="return_notes_{{ $booking->id }}" class="form-label small fw-semibold">Inspection / Return Notes (Optional)</label>
                                            <textarea name="return_notes" id="return_notes_{{ $booking->id }}" class="form-control form-control-sm" rows="3" placeholder="e.g. Returned on time, fuel full, vehicle in good condition..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check me-1" aria-hidden="true"></i>Confirm &amp; Move to Month History
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fa-solid fa-calendar-xmark fa-2x mb-3 text-secondary d-block" aria-hidden="true"></i>
                            No bookings found in this view.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

@if (auth()->user()->role === 'admin')
<div class="modal fade" id="addBookingModal" data-open-on-error="{{ old('_form') === 'booking' || $selectedCustomer ? 'true' : 'false' }}" tabindex="-1" aria-labelledby="addBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="addBookingModalLabel">Add Booking</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form" value="booking">
                <div class="modal-body">
                    <div class="row g-3">
                        @if (auth()->user()->role === 'admin' && $selectedCustomer)
                        <input type="hidden" name="customer_id" value="{{ $selectedCustomer->id }}">
                        <div class="col-12">
                            <label class="form-label">Customer</label>
                            <div class="form-control bg-light">{{ $selectedCustomer->name }} <span class="text-muted">({{ $selectedCustomer->email }})</span></div>
                        </div>
                        @endif
                        <div class="col-12">
                            <label for="bookingCarId" class="form-label">Car</label>
                            <select id="bookingCarId" name="car_id" class="form-select" required>
                                <option value="" disabled selected>Select a car</option>
                                @foreach ($cars as $car)
                                @if ($car->status === 'available')
                                <option value="{{ $car->id }}">{{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        @if (auth()->user()->role === 'admin')
                        <div class="col-md-6">
                            <label for="bookingCustomerName" class="form-label">Customer name</label>
                            <input type="text" class="form-control" id="bookingCustomerName" name="customer_name" value="{{ old('customer_name', $selectedCustomer?->name) }}" @required(!$selectedCustomer)>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerEmail" class="form-label">Customer email</label>
                            <input type="email" class="form-control" id="bookingCustomerEmail" name="customer_email" value="{{ old('customer_email', $selectedCustomer?->email) }}" @required(!$selectedCustomer)>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="bookingCustomerPhone" name="customer_phone" value="{{ old('customer_phone', $selectedCustomer?->phone) }}" @required(!$selectedCustomer)>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="bookingCustomerAddress" name="customer_address" value="{{ old('customer_address', $selectedCustomer?->address) }}">
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label for="bookingStartDate" class="form-label">Start date</label>
                            <input type="date" class="form-control" id="bookingStartDate" name="start_date" min="{{ now()->toDateString() }}" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingEndDate" class="form-label">End date</label>
                            <input type="date" class="form-control" id="bookingEndDate" name="end_date" value="{{ old('end_date') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalWithErrors = document.querySelector('[data-open-on-error="true"]');
        if (modalWithErrors && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalWithErrors).show();
        }
    });
</script>
@endsection
