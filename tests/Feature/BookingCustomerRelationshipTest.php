<?php

use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('moves legacy booking customer references to user accounts', function () {
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

    Schema::table('bookings', function ($table) {
        $table->dropForeign(['customer_id']);
    });
    Schema::table('bookings', function ($table) {
        $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
    });

    $legacyCustomerId = DB::table('customers')->insertGetId([
        'name' => 'Legacy Customer',
        'email' => 'legacy@example.test',
        'password' => Hash::make('legacy-password'),
        'phone' => '555-0100',
        'address' => 'Test address',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $booking = Bookings::create([
        'car_id' => $car->id,
        'customer_id' => $legacyCustomerId,
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-03',
        'total_price' => 200,
    ]);

    $migration = require database_path('migrations/2026_10_18_200000_correct_booking_customer_foreign_key.php');
    $migration->up();

    $user = User::where('email', 'legacy@example.test')->firstOrFail();
    $booking->refresh();

    expect($user->role)->toBe('user')
        ->and($booking->customer_id)->toBe($user->id)
        ->and($booking->customer->is($user))->toBeTrue()
        ->and(collect(Schema::getForeignKeys('bookings'))
            ->firstWhere('columns', ['customer_id'])['foreign_table'])->toBe('users');

    $migration->down();
    $booking->refresh();

    expect($booking->customer_id)->toBe($legacyCustomerId)
        ->and(collect(Schema::getForeignKeys('bookings'))
            ->firstWhere('columns', ['customer_id'])['foreign_table'])->toBe('customers');
});
