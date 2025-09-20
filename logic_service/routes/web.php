<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\vehiclesController;
use App\Http\Controllers\driversController;
use App\Http\Controllers\routesController;
use App\Http\Controllers\tripsController;
use App\Http\Controllers\deliveriesController;
use App\Http\Controllers\incidentsController;
use App\Http\Controllers\rolesController;
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
    
    // Vehicles routes
    Route::resource('vehicles', vehiclesController::class);
    Route::patch('/vehicles/{vehicle}/toggle-status', [vehiclesController::class, 'toggleStatus'])->name('vehicles.toggle-status');
    Route::get('/api/vehicles/status/{status}', [vehiclesController::class, 'getByStatus'])->name('vehicles.by-status');
    
    // Drivers routes
    Route::resource('drivers', driversController::class);
    Route::patch('/drivers/{driver}/toggle-status', [driversController::class, 'toggleStatus'])->name('drivers.toggle-status');
    Route::get('/api/drivers/active', [driversController::class, 'getActiveDrivers'])->name('drivers.active');
    
    // Routes routes
    Route::resource('routes', routesController::class);
    Route::get('/api/routes', [routesController::class, 'getRoutes'])->name('routes.api');
    Route::post('/api/routes/calculate-distance', [routesController::class, 'calculateDistance'])->name('routes.calculate-distance');
    
    // Trips routes
    Route::resource('trips', tripsController::class);
    Route::patch('/trips/{trip}/status', [tripsController::class, 'updateStatus'])->name('trips.update-status');
    Route::patch('/trips/{trip}/start', [tripsController::class, 'startTrip'])->name('trips.start');
    Route::patch('/trips/{trip}/complete', [tripsController::class, 'completeTrip'])->name('trips.complete');
    Route::get('/api/trips/status/{status}', [tripsController::class, 'getByStatus'])->name('trips.by-status');
    
    // Deliveries routes
    Route::resource('deliveries', deliveriesController::class);
    Route::patch('/deliveries/{delivery}/status', [deliveriesController::class, 'updateStatus'])->name('deliveries.update-status');
    Route::patch('/deliveries/{delivery}/delivered', [deliveriesController::class, 'markAsDelivered'])->name('deliveries.mark-delivered');
    Route::patch('/deliveries/{delivery}/failed', [deliveriesController::class, 'markAsFailed'])->name('deliveries.mark-failed');
    Route::get('/api/deliveries/status/{status}', [deliveriesController::class, 'getByStatus'])->name('deliveries.by-status');
    Route::get('/api/deliveries/trip/{trip}', [deliveriesController::class, 'getByTrip'])->name('deliveries.by-trip');
    
    // Incidents routes
    Route::resource('incidents', incidentsController::class);
    Route::patch('/incidents/{incident}/resolve', [incidentsController::class, 'markAsResolved'])->name('incidents.resolve');
    Route::patch('/incidents/{incident}/toggle-resolved', [incidentsController::class, 'toggleResolved'])->name('incidents.toggle-resolved');
    Route::get('/api/incidents/status/{resolved}', [incidentsController::class, 'getByStatus'])->name('incidents.by-status');
    Route::get('/api/incidents/trip/{trip}', [incidentsController::class, 'getByTrip'])->name('incidents.by-trip');
    Route::get('/api/incidents/type/{type}', [incidentsController::class, 'getByType'])->name('incidents.by-type');
    Route::get('/api/incidents/summary', [incidentsController::class, 'getSummary'])->name('incidents.summary');
    
    // Roles routes (Admin only)
    Route::middleware('can:admin')->group(function () {
        Route::resource('roles', rolesController::class);
        Route::get('/api/roles', [rolesController::class, 'getRoles'])->name('roles.api');
        Route::get('/api/roles/{role}/users', [rolesController::class, 'getUsersByRole'])->name('roles.users');
    });
});

require __DIR__.'/auth.php';
