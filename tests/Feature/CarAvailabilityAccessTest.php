<?php

use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createCarForAvailabilityTest(string $brand, string $model, string $plateNumber, string $status): Car
{
    return Car::create([
        'brand' => $brand,
        'model' => $model,
        'year' => 2026,
        'plate_number' => $plateNumber,
        'totalMileage' => 100,
        'daily_rate' => 100,
        'gear_type' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'status' => $status,
    ]);
}

it('only exposes available cars and searches within available cars on the public cars endpoint', function () {
    createCarForAvailabilityTest('Available', 'Sedan', 'AVAILABLE-001', 'available');
    createCarForAvailabilityTest('Unavailable', 'Sedan', 'UNAVAILABLE-001', 'rented');
    createCarForAvailabilityTest('Available', 'SUV', 'AVAILABLE-002', 'available');

    $this->getJson('/api/cars')
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonMissing(['brand' => 'Unavailable']);

    $this->getJson('/api/cars?search=SUV')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['brand' => 'Available', 'model' => 'SUV']);

    $this->getJson('/api/cars?search=Unavailable')
        ->assertOk()
        ->assertJsonCount(0);
});

it('shows the complete searchable inventory only to administrators', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin-cars@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
    $customer = User::create([
        'name' => 'Customer',
        'email' => 'customer-cars@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);
    $availableCar = createCarForAvailabilityTest('Available', 'Sedan', 'AVAILABLE-003', 'available');
    $unavailableCar = createCarForAvailabilityTest('Unavailable', 'SUV', 'UNAVAILABLE-003', 'maintenance');

    expect($customer->can('view', $availableCar))->toBeTrue()
        ->and($customer->can('view', $unavailableCar))->toBeFalse()
        ->and($admin->can('view', $unavailableCar))->toBeTrue();

    $this->actingAs($admin)
        ->get(route('cars.cars_list'))
        ->assertOk()
        ->assertSee('Fleet inventory')
        ->assertSee('Search all cars')
        ->assertSee('cars-table-body');

    $this->actingAs($customer)
        ->get(route('cars.cars_list'))
        ->assertOk()
        ->assertSee('Available cars')
        ->assertSee('Search available cars')
        ->assertDontSee('Fleet inventory')
        ->assertDontSee('id="cars-table-body"', false)
        ->assertDontSee('Add New Car');

    $this->actingAs($customer)->get(route('cars.inventory'))->assertForbidden();

    $this->actingAs($admin)
        ->getJson(route('cars.inventory', ['search' => 'SUV']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['brand' => 'Unavailable', 'model' => 'SUV']);
});

it('prevents a customer from booking a car that is not available', function () {
    $customer = User::create([
        'name' => 'Customer',
        'email' => 'booking-cars@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);
    $car = createCarForAvailabilityTest('Unavailable', 'Car', 'UNAVAILABLE-BOOK', 'rented');

    $this->actingAs($customer)
        ->from(route('cars.cars_list'))
        ->post(route('bookings.store'), [
            'car_id' => $car->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ])
        ->assertRedirect(route('cars.cars_list'))
        ->assertSessionHasErrors('car_id');

    expect(Bookings::count())->toBe(0);
});

it('allows a customer to book an available car', function () {
    $customer = User::create([
        'name' => 'Customer',
        'email' => 'available-booking@example.test',
        'password' => 'password',
        'role' => 'user',
    ]);
    $car = createCarForAvailabilityTest('Available', 'Car', 'AVAILABLE-BOOK', 'available');

    $this->actingAs($customer)
        ->post(route('bookings.store'), [
            'car_id' => $car->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ])
        ->assertRedirect(route('show_booking_list'));

    expect(Bookings::where('customer_id', $customer->id)->count())->toBe(1);
});
