<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\driversController;
use App\Http\Controllers\vehiclesController;
use App\Http\Controllers\routesController;
use App\Http\Controllers\tripsController;
use App\Http\Controllers\deliveriesController;
use App\Http\Controllers\incidentsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Drivers routes
    Route::resource('drivers', driversController::class);

    // Vehicles routes
    Route::resource('vehicles', vehiclesController::class);

    // Routes routes
    Route::resource('routes', routesController::class);

    // Trips routes
    Route::resource('trips', tripsController::class);

    // Deliveries routes
    Route::resource('deliveries', deliveriesController::class);

    // Incidents routes
    Route::resource('incidents', incidentsController::class);
});

require __DIR__ . '/auth.php';
