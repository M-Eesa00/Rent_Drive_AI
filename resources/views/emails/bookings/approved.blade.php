<h1>Your booking request is approved</h1>

<p>Hello {{ $booking->customer->name }},</p>
<p>Your booking has been approved. Your booking reference is <strong>{{ $booking->reference }}</strong>.</p>

<h2>Rental details</h2>
<ul>
    <li>Vehicle: {{ $booking->car->brand }} {{ $booking->car->model }}</li>
    <li>Rental period: {{ $booking->start_date }} to {{ $booking->end_date }}</li>
    <li>Total: Rs {{ number_format((float) $booking->total_price, 2) }}</li>
</ul>

@if ($booking->special_requests)
<p>Special requests: {{ $booking->special_requests }}</p>
@endif

<p>Please keep this reference and bring your driving licence when collecting the vehicle.</p>
