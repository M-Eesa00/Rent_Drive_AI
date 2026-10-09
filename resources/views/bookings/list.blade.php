@extends('layouts.main')

@section('content')

<main>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <div>
            <h1 class="h2 mb-1">Bookings</h1>
            <p class="text-muted mb-0">{{ $bookings->count() }} bookings</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">Add Booking</button>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger mt-3" role="alert">
        <strong>Unable to save the booking.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-hover align-middle">
            <thead>
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
                    <td>{{ $booking->customer?->name ?? 'Customer removed' }}</td>
                    <td>{{ $booking->car ? $booking->car->brand . ' ' . $booking->car->model : 'Car removed' }}</td>
                    <td>{{ $booking->start_date }}</td>
                    <td>{{ $booking->end_date }}</td>
                    <td>Rs{{ number_format($booking->total_price, 2) }}</td>
                    <td>{{ ucfirst($booking->status) }}</td>
                    <td>{{ ucfirst($booking->payment_status) }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this booking?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">No bookings have been added.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<div class="modal fade" id="addBookingModal" data-open-on-error="{{ old('_form') === 'booking' ? 'true' : 'false' }}" tabindex="-1" aria-labelledby="addBookingModalLabel" aria-hidden="true">
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
                        <div class="col-12">
                            <label for="bookingCarId" class="form-label">Car</label>
                            <select id="bookingCarId" name="car_id" class="form-select" required>
                                <option value="" disabled selected>Select a car</option>
                                @foreach ($cars as $car)
                                <option value="{{ $car->id }}">{{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerName" class="form-label">Customer name</label>
                            <input type="text" class="form-control" id="bookingCustomerName" name="customer_name" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerEmail" class="form-label">Customer email</label>
                            <input type="email" class="form-control" id="bookingCustomerEmail" name="customer_email" value="{{ old('customer_email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerPhone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="bookingCustomerPhone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bookingCustomerAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="bookingCustomerAddress" name="customer_address" value="{{ old('customer_address') }}">
                        </div>
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