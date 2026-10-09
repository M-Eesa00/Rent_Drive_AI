<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Car::create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'plate_number' => 'ABC-1234',
            'totalMileage' => 15000.00,
            'daily_rate' => 45.00,
            'gear_type' => 'Automatic',
            'fuel_type' => 'Petrol',
            'seats' => 5,
            'status' => 'available',
        ]);

        Car::create([
            'brand' => 'Honda',
            'model' => 'Civic',
            'year' => 2023,
            'plate_number' => 'XYZ-5678',
            'totalMileage' => 8000.00,
            'daily_rate' => 60.00,
            'gear_type' => 'Automatic',
            'fuel_type' => 'Hybrid',
            'seats' => 5,
            'status' => 'available',
        ]);
    }
}
