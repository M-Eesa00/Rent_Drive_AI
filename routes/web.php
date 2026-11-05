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

Route::get('cars', carsController::class . '@carsList')->name('cars.cars_list');



Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('cars/create', [carsController::class, 'create'])->name('cars.create')->middleware('admin-only');
    Route::get('cars/{car}/edit', [carsController::class, 'edit'])->name('cars.edit')->middleware('admin-only');
    Route::post('cars', [carsController::class, 'store'])->name('cars.store')->middleware('admin-only');
    Route::put('cars/{car}', [carsController::class, 'update'])->name('cars.update')->middleware('admin-only');
    Route::delete('cars/{car}', [carsController::class, 'destroy'])->name('cars.destroy')->middleware('admin-only');
    Route::get('cars/inventory', [carsController::class, 'adminIndex'])->name('cars.inventory')->middleware('admin-only');
    Route::get('bookings', bookingsController::class . '@listBookings')->name('show_booking_list');
    Route::post('bookings', bookingsController::class . '@store')->name('bookings.store');
    Route::get('bookings/{booking}', [bookingsController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/edit', [bookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('bookings/{booking}', [bookingsController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}', [bookingsController::class, 'destroy'])->name('bookings.destroy');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export')->middleware('admin-only');
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('admin-only');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('admin-only');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('admin-only');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('admin-only');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('admin-only');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('admin-only');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->middleware('admin-only');
    Route::get('cars_details', carsController::class . '@carsDetails');
    Route::get('dashboard', dashboardController::class . '@index')->name('dashboard');
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('admin-only');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('admin-only');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('admin-only');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('admin-only');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('admin-only');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('admin-only');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
