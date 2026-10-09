<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bookings;
use App\Models\Car;
use App\Models\User;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $car = Car::first();

        Bookings::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(5),
            'total_price' => 180.00,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
    }
}
