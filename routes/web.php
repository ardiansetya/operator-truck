<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/validate', [AuthController::class, 'validateToken']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // View login dan register
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
});

Route::middleware(['auth.token'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'homeView'])->name('home');
    Route::get('/rent', [DashboardController::class, 'rentView'])->name('rent');
    Route::get('/transit', [DashboardController::class, 'transitView'])->name('transit');


    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{id}', [CityController::class, 'show']);
    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/{id}', [CityController::class, 'update']);
    Route::delete('/cities/{id}', [CityController::class, 'destroy']);

    // Truck
    Route::get('/trucks', [TruckController::class, 'index']);
    Route::get('/trucks/available', [TruckController::class, 'available']);
    Route::get('/trucks/{id}', [TruckController::class, 'show']);
    Route::post('/trucks', [TruckController::class, 'store']);
    Route::put('/trucks/{id}', [TruckController::class, 'update']);
    Route::delete('/trucks/{id}', [TruckController::class, 'destroy']);
    Route::put('/trucks/maintenance/{id}', [TruckController::class, 'maintenance']);

    // Delivery
    Route::get('/deliveries/active', [DeliveryController::class, 'active']);
});
