<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\driversController;
use App\Http\Controllers\vehiclesController;
use App\Http\Controllers\routesController;
use App\Http\Controllers\tripsController;
use App\Http\Controllers\deliveriesController;
use App\Http\Controllers\incidentsController;
use App\Http\Controllers\TripLocationController;
use App\Http\Controllers\reportsController;
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

    //reports 
    Route::get('/reports/excel', [reportsController::class, 'exportExcel'])->name('report.excel');
    Route::get('/reports/pdf',[reportsController::class, 'exportPdf'])->name('report.pdf');
    Route::get('/reports/kpis', [reportsController::class, 'kpis'])->name('report.kpis');

    //trip locations
    Route::middleware('throttle:30,1')->group(function(){
        Route::get('/trips/{trip}/locations',[TripLocationController::class, 'show']);
    })
        ->name('trip.locations.store');
});

require __DIR__ . '/auth.php';
