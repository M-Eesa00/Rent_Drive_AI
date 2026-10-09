<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'year',
        'model',
        'plate_number',
        'totalMileage',
        'gear_type',
        'fuel_type',
        'seats',
        'status',
        'daily_rate',
        'image'
    ];

    public function bookings()
    {
        return $this->hasMany(Bookings::class);
    }
}
