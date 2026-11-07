<?php

use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createReturnLifecycleCar(string $plateNumber): Car
{
    return Car::create([
        'brand' => 'Return Test',
        'model' => 'Sedan',
        'year' => 2026,
        'plate_number' => $plateNumber,
        'totalMileage' => 100,
        'daily_rate' => 100,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'status' => 'rented',
    ]);
}

function createReturnLifecycleUser(string $email, string $role): User
{
    return User::create([
        'name' => ucfirst($role),
        'email' => $email,
        'password' => 'password',
        'role' => $role,
    ]);
}

function createReturnLifecycleBooking(Car $car, User $customer, string $endDate): Bookings
{
    return Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => '2026-11-01',
        'end_date' => $endDate,
        'total_price' => 500,
        'status' => 'active',
        'payment_status' => 'unpaid',
    ]);
}

it('archives a requested return after admin confirmation and makes the car available', function () {
    $this->travelTo(now()->setDate(2026, 11, 7)->setTime(12, 0));
    $customer = createReturnLifecycleUser('return-customer@example.test', 'user');
    $admin = createReturnLifecycleUser('return-admin@example.test', 'admin');
    $car = createReturnLifecycleCar('RETURN-001');
    $booking = createReturnLifecycleBooking($car, $customer, '2026-11-10');

    $this->actingAs($customer)
        ->post(route('bookings.request_return', $booking))
        ->assertRedirect();

    expect($booking->fresh()->return_requested_at)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('bookings.confirm_return', $booking), [
            'payment_status' => 'paid',
            'return_notes' => 'Vehicle inspected.',
        ])
        ->assertRedirect(route('show_booking_list'));

    expect($booking->fresh()->status)->toBe('completed')
        ->and($booking->fresh()->returned_at)->not->toBeNull()
        ->and($booking->fresh()->return_notes)->toBe('Vehicle inspected.')
        ->and($car->fresh()->status)->toBe('available');

    $this->actingAs($admin)
        ->get(route('show_booking_list'))
        ->assertDontSee('confirmReturnModal-'.$booking->id, false);

    $this->get(route('bookings.history', ['month' => '2026-11']))
        ->assertOk()
        ->assertSee('RETURN-001')
        ->assertSee('Vehicle inspected.');
});

it('shows bookings as due after the scheduled return date without changing their status', function () {
    $this->travelTo(now()->setDate(2026, 11, 8)->setTime(12, 0));
    $customer = createReturnLifecycleUser('due-customer@example.test', 'user');
    $admin = createReturnLifecycleUser('due-admin@example.test', 'admin');
    $car = createReturnLifecycleCar('RETURN-002');
    $booking = createReturnLifecycleBooking($car, $customer, '2026-11-07');

    $this->actingAs($admin)
        ->get(route('show_booking_list', ['filter' => 'due']))
        ->assertOk()
        ->assertSee('RETURN-002')
        ->assertSee('Confirm Return');

    expect($booking->fresh()->status)->toBe('active')
        ->and($booking->fresh()->return_requested_at)->toBeNull();
});

it('waits until the scheduled return date has ended before marking the booking due', function () {
    $this->travelTo(now()->setDate(2026, 11, 8)->setTime(12, 0));
    $customer = createReturnLifecycleUser('deadline-customer@example.test', 'user');
    $admin = createReturnLifecycleUser('deadline-admin@example.test', 'admin');
    $car = createReturnLifecycleCar('RETURN-005');
    $booking = createReturnLifecycleBooking($car, $customer, '2026-11-08');

    $this->actingAs($admin)
        ->get(route('show_booking_list', ['filter' => 'due']))
        ->assertOk()
        ->assertDontSee('confirmReturnModal-'.$booking->id, false);

    $this->travelTo(now()->setDate(2026, 11, 9)->startOfDay());

    $this->get(route('show_booking_list', ['filter' => 'due']))
        ->assertOk()
        ->assertSee('RETURN-005');
});

it('does not allow admin confirmation before due date unless the customer requests return', function () {
    $this->travelTo(now()->setDate(2026, 11, 7)->setTime(12, 0));
    $customer = createReturnLifecycleUser('early-customer@example.test', 'user');
    $admin = createReturnLifecycleUser('early-admin@example.test', 'admin');
    $car = createReturnLifecycleCar('RETURN-003');
    $booking = createReturnLifecycleBooking($car, $customer, '2026-11-10');

    $this->actingAs($admin)
        ->post(route('bookings.confirm_return', $booking))
        ->assertForbidden();

    expect($booking->fresh()->status)->toBe('active')
        ->and($car->fresh()->status)->toBe('rented');
});

it('does not allow customers to complete a booking through the CRUD update endpoint', function () {
    $customer = createReturnLifecycleUser('update-customer@example.test', 'user');
    $car = createReturnLifecycleCar('RETURN-004');
    $booking = createReturnLifecycleBooking($car, $customer, now()->addDays(3)->toDateString());

    $this->actingAs($customer)
        ->from(route('bookings.edit', $booking))
        ->put(route('bookings.update', $booking), [
            'car_id' => $car->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => 'completed',
            'payment_status' => 'unpaid',
        ])
        ->assertRedirect(route('bookings.edit', $booking))
        ->assertSessionHasErrors('status');

    expect($booking->fresh()->status)->toBe('active')
        ->and($car->fresh()->status)->toBe('rented');
});
