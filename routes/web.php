<?php

use App\Http\Controllers\bookingsController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\carsController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('cars/create', [carsController::class, 'create'])->name('cars.create')->middleware('admin-only');
    Route::get('cars/{car}/edit', [carsController::class, 'edit'])->name('cars.edit');
    Route::post('cars', [carsController::class, 'store'])->name('cars.store');
    Route::put('cars/{car}', [carsController::class, 'update'])->name('cars.update')->middleware('admin-only');
    Route::delete('cars/{car}', [carsController::class, 'destroy'])->name('cars.destroy')->middleware('admin-only');
    Route::get('bookings', bookingsController::class . '@listBookings')->name('show_booking_list');
    Route::post('bookings', bookingsController::class . '@store')->name('bookings.store');
    Route::get('bookings/{booking}/edit', [bookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}', [bookingsController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}', [bookingsController::class, 'destroy'])->name('bookings.destroy');
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('cars_details', carsController::class . '@carsDetails');
    Route::get('dashboard', dashboardController::class . '@index')->name('dashboard');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('cars', carsController::class . '@carsList')->name('cars.cars_list');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
