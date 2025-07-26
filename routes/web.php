<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TransitPointController;
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

Route::middleware([ 'auth.token.refresh', 'role:ADMIN,DRIVER'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'homeView'])->name('dashboard.index');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

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

    // Transit Point Routes
    Route::get('/transit-points', [TransitPointController::class, 'index'])->name('transit-points.index');
    Route::get('/transit-points/create', [TransitPointController::class, 'create'])->name('transit-points.create');
    Route::post('/transit-points', [TransitPointController::class, 'store'])->name('transit-points.store');
    Route::get('/transit-points/{id}', [TransitPointController::class, 'show'])->name('transit-points.show');
    Route::get('/transit-points/{id}/edit', [TransitPointController::class, 'edit'])->name('transit-points.edit');
    Route::put('/transit-points/{id}', [TransitPointController::class, 'update'])->name('transit-points.update');
    Route::delete('/transit-points/{id}', [TransitPointController::class, 'destroy'])->name('transit-points.destroy');

    // Route Management
   
    Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
    Route::get('/routes/create', [RouteController::class, 'create'])->name('routes.create');
    Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');
    Route::get('/routes/{id}', [RouteController::class, 'show'])->name('routes.show');
    Route::get('/routes/{id}/edit', [RouteController::class, 'edit'])->name('routes.edit');
    Route::put('/routes/{id}', [RouteController::class, 'update'])->name('routes.update');
    Route::delete('/routes/{id}', [RouteController::class, 'destroy'])->name('routes.destroy');
});

Route::get('/debug-token', function () {
    $token = session('access_token');
    if (!$token) {
        return response()->json(['error' => 'No token found in session']);
    }
    $baseUrl = env('JAVA_BACKEND_URL', 'http://localhost:8080');
    if (empty($baseUrl)) {
        return response()->json(['error' => 'JAVA_BACKEND_URL is not configured'], 500);
    }
    $response = \Illuminate\Support\Facades\Http::withToken($token)
        ->withHeaders(['Accept' => 'application/json'])
        ->get($baseUrl . '/api/users/profile');
    return response()->json([
        'token' => substr($token, 0, 10) . '...',
        'profile_response' => $response->json(),
        'status' => $response->status(),
        'url' => $baseUrl . '/api/users/profile', 
    ]);
});