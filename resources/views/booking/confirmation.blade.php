@extends('layouts.main')

@section('content')
<main class="py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 760px;">
        <div class="card-body p-4 p-md-5 text-center">
            @if (session('success'))
            <div class="alert alert-success text-start" role="status">{{ session('success') }}</div>
            @endif
            @if (session('warning'))
            <div class="alert alert-warning text-start" role="alert">{{ session('warning') }}</div>
            @endif

            @if ($booking->status === 'confirmed')
            <div class="text-success mb-3"><i class="fa-solid fa-circle-check fa-3x" aria-hidden="true"></i></div>
            <h1 class="h2 fw-bold">Booking approved</h1>
            <p class="text-muted">
                The admin approved your booking request.
                @if ($booking->customer_registered)
                    @if ($booking->email_sent_at)
                    A confirmation email has been sent to {{ $booking->customer->email }}.
                    @else
                    Your confirmation email has not been sent yet. Please contact the rental team and provide your booking reference.
                    @endif
                @else
                This guest booking does not have a registered customer account, so no customer email is sent.
                @endif
            </p>
            @elseif ($booking->status === 'cancelled')
            <div class="text-warning mb-3"><i class="fa-solid fa-circle-exclamation fa-3x" aria-hidden="true"></i></div>
            <h1 class="h2 fw-bold">Booking request cancelled</h1>
            <p class="text-muted">This booking is no longer active. You can start a new search to choose another vehicle or date range.</p>
            @else
            <div class="text-primary mb-3"><i class="fa-solid fa-hourglass-half fa-3x" aria-hidden="true"></i></div>
            <h1 class="h2 fw-bold">Awaiting admin approval</h1>
            <p class="text-muted">Your booking request is saved. The admin will review it; this page refreshes automatically to show the decision.</p>
            <script>
                window.setTimeout(() => window.location.reload(), 10000);
            </script>
            @endif

            <div class="bg-light rounded p-3 my-4 text-start">
                <p class="small text-muted mb-1">Booking reference</p>
                <p class="h5 fw-bold mb-3">{{ $booking->reference }}</p>
                <p class="mb-1"><strong>Vehicle:</strong> {{ $booking->car->brand }} {{ $booking->car->model }}</p>
                <p class="mb-1"><strong>Dates:</strong> {{ $booking->start_date }} to {{ $booking->end_date }}</p>
                <p class="mb-0"><strong>Total:</strong> Rs {{ number_format((float) $booking->total_price, 2) }}</p>
            </div>

            <a href="{{ route('booking.create') }}" class="btn btn-primary">Start another booking</a>
        </div>
    </div>
</main>
@endsection
