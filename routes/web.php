<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

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
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'homeView'])->name('dashboard.index');

    // City Routes
    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('/cities/create', [CityController::class, 'create'])->name('cities.create');
    Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
    Route::get('/cities/{id}/edit', [CityController::class, 'edit'])->name('cities.edit');
    Route::put('/cities/{id}', [CityController::class, 'update'])->name('cities.update');
    Route::delete('/cities/{id}', [CityController::class, 'destroy'])->name('cities.destroy');

    // Truck Routes
    Route::get('/trucks', [TruckController::class, 'index'])->name('trucks.index');
    Route::get('/trucks/create', [TruckController::class, 'create'])->name('trucks.create');
    Route::post('/trucks', [TruckController::class, 'store'])->name('trucks.store');
    Route::get('/trucks/{id}/edit', [TruckController::class, 'edit'])->name('trucks.edit');
    Route::put('/trucks/{id}', [TruckController::class, 'update'])->name('trucks.update');
    Route::delete('/trucks/{id}', [TruckController::class, 'destroy'])->name('trucks.destroy');

    // Delivery Routes
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/create', [DeliveryController::class, 'create'])->name('deliveries.create');
    Route::post('/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::get('/deliveries/{id}', [DeliveryController::class, 'show'])->name('deliveries.show');
    Route::post('/deliveries/{id}/finish', [DeliveryController::class, 'finish'])->name('deliveries.finish');
    Route::delete('/deliveries/{id}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');
});
