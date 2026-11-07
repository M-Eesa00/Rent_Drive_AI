<?php

use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows customer details and every associated booking to administrators', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
    $customer = User::create([
        'name' => 'Alex Customer',
        'email' => 'alex@example.test',
        'password' => 'password',
        'role' => 'user',
        'phone' => '555-0100',
        'address' => '123 Main Street',
    ]);
    $car = Car::create([
        'brand' => 'Test',
        'model' => 'Car',
        'year' => 2026,
        'plate_number' => 'TEST-001',
        'totalMileage' => 100,
        'daily_rate' => 100,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'status' => 'available',
    ]);

    Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-03',
        'total_price' => 200,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);
    Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'total_price' => 100,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($admin)
        ->get(route('customers.show', $customer))
        ->assertOk()
        ->assertSee('Alex Customer')
        ->assertSee('alex@example.test')
        ->assertSee('555-0100')
        ->assertSee('123 Main Street')
        ->assertSee('Booking history')
        ->assertSee('Test Car')
        ->assertSee('Rs300.00')
        ->assertSee(route('users.index'), false)
        ->assertSee('Confirmed')
        ->assertSee('Pending');
});

it('hides admin navigation and denies customer accounts access to admin pages', function () {
    $customer = User::create([
        'name' => 'Regular Customer',
        'email' => 'customer@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);

    $this->actingAs($customer)
        ->get(route('show_booking_list'))
        ->assertOk()
        ->assertDontSee('Users')
        ->assertDontSee('Customers')
        ->assertDontSee(route('users.index'), false);

    $this->get(route('users.index'))->assertForbidden();
    $this->get(route('customers.index'))->assertForbidden();
    $this->get(route('customers.show', $customer))->assertForbidden();
});

it('shows customers a dashboard containing only their own account and bookings', function () {
    $customer = User::create([
        'name' => 'Dashboard Customer',
        'email' => 'dashboard-customer@example.test',
        'password' => 'password',
        'role' => 'user',
        'phone' => '555-0101',
        'address' => 'Customer address',
    ]);
    $otherCustomer = User::create([
        'name' => 'Other Customer',
        'email' => 'other-customer@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);
    $car = Car::create([
        'brand' => 'Dashboard',
        'model' => 'Vehicle',
        'year' => 2026,
        'plate_number' => 'DASH-001',
        'totalMileage' => 100,
        'daily_rate' => 100,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'status' => 'available',
    ]);

    Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->subDays(3)->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'total_price' => 200,
        'status' => 'active',
        'payment_status' => 'paid',
    ]);
    Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'total_price' => 250,
        'status' => 'confirmed',
        'payment_status' => 'unpaid',
    ]);
    Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $otherCustomer->id,
        'start_date' => now()->addDays(10)->toDateString(),
        'end_date' => now()->addDays(11)->toDateString(),
        'total_price' => 980,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Welcome, Dashboard Customer')
        ->assertSee('dashboard-customer@example.test')
        ->assertSee('Customer address')
        ->assertSee('Total bookings')
        ->assertSee('Upcoming bookings')
        ->assertSee('Active rentals')
        ->assertSee('Paid booking total')
        ->assertSee('Rs 200.00')
        ->assertSeeInOrder(['Paid booking total', 'Rs 200.00'])
        ->assertSee('Dashboard Vehicle')
        ->assertDontSee('Rs 980.00')
        ->assertSee('My Dashboard');
});

it('keeps the management dashboard available to administrators', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'dashboard-admin@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Car rental management')
        ->assertSee('Key performance indicators')
        ->assertSee('Customer directory');
});

it('lets admins start a booking for a customer and assigns the booking to that customer', function () {
    $admin = User::create([
        'name' => 'Booking Admin',
        'email' => 'booking-admin@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
    $customer = User::create([
        'name' => 'Selected Customer',
        'email' => 'selected-customer@example.test',
        'password' => 'password',
        'role' => 'user',
        'phone' => '555-0123',
    ]);
    $car = Car::create([
        'brand' => 'Booking',
        'model' => 'Car',
        'year' => 2026,
        'plate_number' => 'BOOK-001',
        'totalMileage' => 100,
        'daily_rate' => 100,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'status' => 'available',
    ]);

    $this->actingAs($admin)
        ->get(route('customers.index'))
        ->assertOk()
        ->assertSee(route('show_booking_list', ['customer_id' => $customer->id]), false);

    $this->get(route('show_booking_list', ['customer_id' => $customer->id]))
        ->assertOk()
        ->assertSee('Selected Customer')
        ->assertSee('data-open-on-error="true"', false)
        ->assertSee('name="customer_id" value="'.$customer->id.'"', false);

    $this->post(route('bookings.store'), [
        'car_id' => $car->id,
        'customer_id' => $customer->id,
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(4)->toDateString(),
    ])->assertRedirect(route('show_booking_list'));

    expect(Bookings::where('customer_id', $customer->id)->count())->toBe(1)
        ->and($customer->fresh()->name)->toBe('Selected Customer');
});

it('does not allow customers to bypass the multi-step booking flow with an admin-selected customer id', function () {
    $customer = User::create([
        'name' => 'Regular Customer',
        'email' => 'regular-booking-customer@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);
    $otherCustomer = User::create([
        'name' => 'Other Customer',
        'email' => 'other-booking-customer@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);
    $car = Car::create([
        'brand' => 'Booking',
        'model' => 'Car',
        'year' => 2026,
        'plate_number' => 'BOOK-002',
        'totalMileage' => 100,
        'daily_rate' => 100,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'status' => 'available',
    ]);

    $this->actingAs($customer)
        ->from(route('cars.cars_list'))
        ->post(route('bookings.store'), [
            'car_id' => $car->id,
            'customer_id' => $otherCustomer->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ])
        ->assertForbidden();

    expect(Bookings::count())->toBe(0);
});
