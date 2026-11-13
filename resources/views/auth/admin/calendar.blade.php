@extends('layouts.main')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold">Bookings calendar</h1>
    <p class="text-muted">See bookings by car and check availability for selected dates.</p>
</div>

<div
    id="booking-calendar"
    data-events-url="{{ route('admin.calendar.events') }}"
    class="mb-5">
</div>

<section class="card p-3">
    <h2 class="h5">Check available cars</h2>

    <form id="availability-form" class="row g-3">
        <div class="col-sm-4">
            <label for="availability-start" class="form-label">Start date</label>
            <input
                id="availability-start"
                class="form-control"
                type="date"
                value="{{ now()->toDateString() }}"
                required>
        </div>

        <div class="col-sm-4">
            <label for="availability-end" class="form-label">End date</label>
            <input
                id="availability-end"
                class="form-control"
                type="date"
                value="{{ now()->toDateString() }}"
                required>
        </div>

        <div class="col-sm-4 d-flex align-items-end">
            <button class="btn btn-primary" type="submit">Check availability</button>
        </div>
    </form>

    <p id="availability-message" class="mt-3 mb-1" role="status"></p>
    <ul id="available-cars" class="mb-0"></ul>
</section>
@endsection

@section('scripts')
@vite('resources/js/calendar.js')
@endsection