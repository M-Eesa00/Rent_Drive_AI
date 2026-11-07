<?php

use App\Mail\BookingApprovedMail;
use App\Mail\BookingRequestMail;
use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

function createWizardCar(string $plateNumber, string $status = 'available'): Car
{
    return Car::create([
        'brand' => 'Wizard',
        'model' => 'Crossover',
        'year' => 2026,
        'plate_number' => $plateNumber,
        'totalMileage' => 100,
        'daily_rate' => 250,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Hybrid',
        'seats' => 5,
        'status' => $status,
    ]);
}

function createWizardCustomer(string $email): User
{
    return User::create([
        'name' => 'Existing Customer',
        'email' => $email,
        'password' => 'password',
        'role' => 'user',
        'phone' => '555-0101',
    ]);
}

function validWizardPayload(Car $car): array
{
    return [
        'car_id' => $car->id,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'customer_name' => 'Guest Customer',
        'customer_email' => 'guest-booking@example.test',
        'customer_phone' => '555-0199',
        'special_requests' => 'Child seat, if available.',
        'booking_acknowledged' => '1',
    ];
}

it('shows the guest booking wizard with staged trip and customer details', function () {
    $this->get(route('booking.create'))
        ->assertOk()
        ->assertSee('Where and when is your trip?')
        ->assertSee('Find available vehicles')
        ->assertSee('Full name')
        ->assertSee('Review your booking')
        ->assertDontSee('pickup_location')
        ->assertDontSee('return_location')
        ->assertDontSee('id="bookingMap"', false)
        ->assertSee('admin approves it');
});

it('sends the homepage Book a Car button directly to the booking wizard', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Book a Car')
        ->assertSee('href="'.route('booking.create').'"', false);
});

it('removes map, payment gateway, and pickup/return location fields from bookings', function () {
    foreach ([
        'pickup_latitude',
        'pickup_longitude',
        'return_latitude',
        'return_longitude',
        'stripe_checkout_session_id',
        'pickup_location',
        'return_location',
    ] as $column) {
        expect(Schema::hasColumn('bookings', $column))->toBeFalse();
    }
});

it('searches vehicles by date range and excludes overlapping reservations', function () {
    $startDate = now()->addDays(5)->toDateString();
    $endDate = now()->addDays(7)->toDateString();
    $availableCar = createWizardCar('WIZARD-AVAILABLE');
    $reservedCar = createWizardCar('WIZARD-RESERVED', 'rented');
    $customer = createWizardCustomer('wizard-reservation@example.test');

    Bookings::create([
        'car_id' => $reservedCar->id,
        'customer_id' => $customer->id,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'total_price' => 500,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $this->getJson(route('booking.availability', [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'cars')
        ->assertJsonFragment(['id' => $availableCar->id])
        ->assertJsonMissing(['id' => $reservedCar->id])
        ->assertJsonFragment(['total_price' => 500]);
});

it('saves a guest booking request and notifies the admin', function () {
    Mail::fake();
    config(['services.booking.admin_email' => 'admin@example.test']);
    $car = createWizardCar('WIZARD-REQUEST');

    $this->post(route('booking.submit'), validWizardPayload($car))
        ->assertRedirect();

    $booking = Bookings::with('customer')->firstOrFail();
    expect($booking->status)->toBe('pending')
        ->and($booking->payment_status)->toBe('unpaid')
        ->and($booking->reference)->toStartWith('RD-')
        ->and($booking->customer_registered)->toBeFalse()
        ->and($booking->special_requests)->toBe('Child seat, if available.')
        ->and($booking->customer->email)->toBe('guest-booking@example.test')
        ->and($car->fresh()->status)->toBe('rented');

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'pending',
        'customer_registered' => false,
    ]);
    Mail::assertSent(BookingRequestMail::class, fn (BookingRequestMail $mail) => $mail->hasTo('admin@example.test'));
    Mail::assertNotSent(BookingApprovedMail::class);
});

it('marks a request from an existing customer account for approval email', function () {
    Mail::fake();
    config(['services.booking.admin_email' => 'admin@example.test']);
    $customer = createWizardCustomer('registered-customer@example.test');
    $car = createWizardCar('WIZARD-REGISTERED');
    $payload = validWizardPayload($car);
    $payload['customer_email'] = $customer->email;

    $this->post(route('booking.submit'), $payload)->assertRedirect();

    $booking = Bookings::firstOrFail();
    expect($booking->customer_id)->toBe($customer->id)
        ->and($booking->customer_registered)->toBeTrue();
});

it('rechecks date conflicts before saving the booking request', function () {
    $car = createWizardCar('WIZARD-RACE', 'rented');
    $customer = createWizardCustomer('date-conflict@example.test');
    $bookingData = validWizardPayload($car);
    Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => $bookingData['start_date'],
        'end_date' => $bookingData['end_date'],
        'total_price' => 500,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $this->from(route('booking.create'))
        ->post(route('booking.submit'), $bookingData)
        ->assertRedirect(route('booking.create'))
        ->assertSessionHasErrors('car_id');

    expect(Bookings::count())->toBe(1);
});

it('approves a booking and emails the registered customer', function () {
    Mail::fake();
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin-approval@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
    $customer = createWizardCustomer('approved-customer@example.test');
    $car = createWizardCar('WIZARD-APPROVED', 'rented');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'total_price' => 500,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'reference' => 'RD-TEST-APPROVED',
        'customer_registered' => true,
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.approve', $booking))
        ->assertRedirect(route('show_booking_list'));

    expect($booking->fresh()->status)->toBe('confirmed')
        ->and($booking->fresh()->email_sent_at)->not->toBeNull();
    Mail::assertSent(BookingApprovedMail::class, fn (BookingApprovedMail $mail) => $mail->hasTo($customer->email));
});

it('approves a guest booking without emailing a customer account that was created for the request', function () {
    Mail::fake();
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin-guest-approval@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
    $customer = createWizardCustomer('guest-account@example.test');
    $car = createWizardCar('WIZARD-GUEST-APPROVED', 'rented');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'total_price' => 500,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'reference' => 'RD-TEST-GUEST-APPROVED',
        'customer_registered' => false,
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.approve', $booking))
        ->assertRedirect(route('show_booking_list'));

    expect($booking->fresh()->status)->toBe('confirmed');
    Mail::assertNotSent(BookingApprovedMail::class);
});

it('does not allow a customer to approve a booking request', function () {
    $customer = createWizardCustomer('not-admin@example.test');
    $car = createWizardCar('WIZARD-NOT-ADMIN', 'rented');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'total_price' => 500,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($customer)
        ->post(route('bookings.approve', $booking))
        ->assertForbidden();

    expect($booking->fresh()->status)->toBe('pending');
});

it('does not allow customers to change booking or payment status', function () {
    $customer = createWizardCustomer('protected-booking@example.test');
    $car = createWizardCar('WIZARD-PROTECTED', 'rented');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'total_price' => 500,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($customer)
        ->put(route('bookings.update', $booking), [
            'car_id' => $car->id,
            'start_date' => $booking->start_date,
            'end_date' => $booking->end_date,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ])
        ->assertSessionHasErrors(['status', 'payment_status']);

    expect($booking->fresh()->status)->toBe('pending')
        ->and($booking->fresh()->payment_status)->toBe('unpaid');
});
