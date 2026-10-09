<?php

use App\Http\Controllers\bookingsController;
use App\Http\Controllers\carsController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/cars', [carsController::class, 'index']);
Route::get('/bookings', bookingsController::class . '@index');
Route::get('/customers', [CustomerController::class, 'apiIndex']);
Route::post('/customers', [CustomerController::class, 'store']);
