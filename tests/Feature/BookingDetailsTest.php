<?php

use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createBookingDetailsCar(string $plateNumber): Car
{
    return Car::create([
        'brand' => 'Touring',
        'model' => 'Sedan',
        'year' => 2025,
        'plate_number' => $plateNumber,
        'totalMileage' => 12000,
        'daily_rate' => 180,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Hybrid',
        'seats' => 5,
        'status' => 'available',
    ]);
}

function createBookingDetailsUser(string $name, string $email, string $role = 'user'): User
{
    return User::create([
        'name' => $name,
        'email' => $email,
        'password' => 'password',
        'role' => $role,
        'phone' => '555-0150',
    ]);
}

it('shows the authenticated customer professional booking and vehicle details', function () {
    $customer = createBookingDetailsUser('Booking Owner', 'booking-owner@example.test');
    $car = createBookingDetailsCar('DETAIL-001');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-04',
        'total_price' => 540,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $this->actingAs($customer)
        ->get(route('bookings.show', $booking))
        ->assertOk()
        ->assertSee('Booking details')
        ->assertSee('Reference #' . $booking->id)
        ->assertSee('Confirmed')
        ->assertSee('Paid')
        ->assertSee('Touring Sedan')
        ->assertSee('DETAIL-001')
        ->assertSee('Automatic')
        ->assertSee('Hybrid')
        ->assertSee('Nov 1, 2026')
        ->assertSee('Nov 4, 2026')
        ->assertSee('3 days')
        ->assertSee('Rs 540.00');
});

it('allows administrators to see booking details including customer contact information', function () {
    $admin = createBookingDetailsUser('Fleet Admin', 'fleet-admin@example.test', 'admin');
    $customer = createBookingDetailsUser('Booking Customer', 'booking-customer@example.test');
    $car = createBookingDetailsCar('DETAIL-002');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'total_price' => 180,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($admin)
        ->get(route('bookings.show', $booking))
        ->assertOk()
        ->assertSee('Booking Customer')
        ->assertSee('booking-customer@example.test')
        ->assertSee('555-0150');
});

it('prevents customers from viewing another customers booking details', function () {
    $owner = createBookingDetailsUser('Booking Owner', 'booking-owner-private@example.test');
    $anotherCustomer = createBookingDetailsUser('Another Customer', 'another-customer-private@example.test');
    $car = createBookingDetailsCar('DETAIL-003');
    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $owner->id,
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'total_price' => 180,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($anotherCustomer)
        ->get(route('bookings.show', $booking))
        ->assertForbidden();
});
