@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h2 mb-0">Edit Booking #{{ $booking->id }}</h1>
                <a href="{{ route('show_booking_list') }}" class="btn btn-outline-secondary">Back to Bookings</a>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="bookingCar" class="form-label">Car</label>
                        <select id="bookingCar" name="car_id" class="form-select" required>
                            @foreach ($cars as $car)
                            <option value="{{ $car->id }}" @selected((string) old('car_id', $booking->car_id) === (string) $car->id)>{{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="bookingCustomer" class="form-label">Customer</label>
                        <select id="bookingCustomer" name="customer_id" class="form-select" required>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) old('customer_id', $booking->customer_id) === (string) $customer->id)>{{ $customer->name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="bookingStart" class="form-label">Start date</label>
                        <input type="date" id="bookingStart" name="start_date" class="form-control" value="{{ old('start_date', $booking->start_date) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="bookingEnd" class="form-label">End date</label>
                        <input type="date" id="bookingEnd" name="end_date" class="form-control" value="{{ old('end_date', $booking->end_date) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="bookingStatus" class="form-label">Booking status</label>
                        <select id="bookingStatus" name="status" class="form-select" required>
                            @foreach (['pending', 'confirmed', 'active', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $booking->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="bookingPaymentStatus" class="form-label">Payment status</label>
                        <select id="bookingPaymentStatus" name="payment_status" class="form-select" required>
                            @foreach (['unpaid', 'paid', 'refunded'] as $paymentStatus)
                            <option value="{{ $paymentStatus }}" @selected(old('payment_status', $booking->payment_status) === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('show_booking_list') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Booking</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection