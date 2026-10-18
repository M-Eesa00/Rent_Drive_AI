<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Bookings extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'customer_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'payment_status'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
