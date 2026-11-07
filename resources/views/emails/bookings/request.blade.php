<h1>New booking request</h1>

<p>A customer booking request is waiting for admin approval.</p>

<h2>Booking details</h2>
<ul>
    <li>Reference: {{ $booking->reference }}</li>
    <li>Customer: {{ $booking->customer->name }} ({{ $booking->customer->email }})</li>
    <li>Phone: {{ $booking->customer->phone ?: 'Not provided' }}</li>
    <li>Vehicle: {{ $booking->car->brand }} {{ $booking->car->model }} ({{ $booking->car->plate_number }})</li>
    <li>Rental period: {{ $booking->start_date }} to {{ $booking->end_date }}</li>
    <li>Total: Rs {{ number_format((float) $booking->total_price, 2) }}</li>
</ul>

@if ($booking->special_requests)
<p>Special requests: {{ $booking->special_requests }}</p>
@endif

<p>Review this request in the administrator booking list.</p>
