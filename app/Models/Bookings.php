<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use app\Controller\App\Http\Controllers\CustomerController;

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
        return $this->belongsTo(Customer::class);
    }
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
