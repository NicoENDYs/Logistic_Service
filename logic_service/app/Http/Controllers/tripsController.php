<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class tripsController extends Controller
{
    //mostrar los viajes en la bd
    public function index(): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('trips.index', compact('trips'));
    }

    //mostrar formulario de creación de viaje
    public function create(): View
    {
        $vehicles = Vehicle::active()->orderBy('plate_number')->get();
        $drivers = Driver::active()->with('user')->orderBy('created_at')->get();
        $routes = Route::orderBy('origin')->get();
        $statuses = Trip::getStatuses();
        
        return view('trips.create', compact('vehicles', 'drivers', 'routes', 'statuses'));
    }

    //agregar un nuevo viaje / insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'route_id' => ['required', 'integer', 'exists:routes,id'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'status' => ['required', 'string', Rule::in(['pendiente', 'en_progreso', 'completado', 'cancelado'])],
        ]);

        // Verificar disponibilidad del vehículo
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if (!$vehicle->isActive()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El vehículo seleccionado no está disponible.');
        }

        // Verificar disponibilidad del conductor
        $driver = Driver::find($validated['driver_id']);
        if (!$driver->isActive()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El conductor seleccionado no está disponible.');
        }

        Trip::create($validated);

        return redirect()->route('trips.index')
            ->with('success', 'Viaje creado exitosamente.');
    }

    //Mostrar un viaje
    public function show(Trip $trip): View
    {
        $trip->load([
            'vehicle', 
            'driver.user', 
            'route', 
            'deliveries', 
            'incidents'
        ]);
        
        return view('trips.show', compact('trip'));
    }

    //abrir formulario de editar viaje
    public function edit(Trip $trip): View
    {
        $vehicles = Vehicle::where('status', 'activo')
            ->orWhere('id', $trip->vehicle_id)
            ->orderBy('plate_number')
            ->get();
            
        $drivers = Driver::with('user')
            ->where('status', 'activo')
            ->orWhere('id', $trip->driver_id)
            ->orderBy('created_at')
            ->get();
            
        $routes = Route::orderBy('origin')->get();
        $statuses = Trip::getStatuses();
        
        return view('trips.edit', compact('trip', 'vehicles', 'drivers', 'routes', 'statuses'));
    }

    //Actualizar / editar viaje
    public function update(Request $request, Trip $trip): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'route_id' => ['required', 'integer', 'exists:routes,id'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'status' => ['required', 'string', Rule::in(['pendiente', 'en_progreso', 'completado', 'cancelado'])],
        ]);

        $trip->update($validated);

        return redirect()->route('trips.index')
            ->with('success', 'Viaje actualizado exitosamente.');
    }

    //eliminar viaje
    public function destroy(Trip $trip): RedirectResponse
    {
        // Solo permitir eliminar viajes pendientes o cancelados
        if (!$trip->canBeDeleted()) {
            return redirect()->route('trips.index')
                ->with('error', 'No se puede eliminar un viaje en progreso o completado.');
        }

        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Viaje eliminado exitosamente.');
    }

    //cambiar estado del viaje
    public function updateStatus(Request $request, Trip $trip): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pendiente', 'en_progreso', 'completado', 'cancelado'])],
        ]);

        if ($trip->changeStatus($validated['status'])) {
            return redirect()->route('trips.index')
                ->with('success', "Estado del viaje cambiado a: {$validated['status']}");
        }
        
        return redirect()->back()
            ->with('error', 'No se pudo cambiar el estado del viaje.');
    }

    // iniciar viaje
    public function startTrip(Trip $trip): RedirectResponse
    {
        if ($trip->start()) {
            return redirect()->route('trips.show', $trip)
                ->with('success', 'Viaje iniciado exitosamente.');
        }
        
        return redirect()->route('trips.index')
            ->with('error', 'Solo se pueden iniciar viajes pendientes.');
    }

    // finalizar viaje
    public function completeTrip(Trip $trip): RedirectResponse
    {
        if ($trip->complete()) {
            return redirect()->route('trips.show', $trip)
                ->with('success', 'Viaje completado exitosamente.');
        }
        
        return redirect()->route('trips.index')
            ->with('error', 'Solo se pueden completar viajes en progreso.');
    }

    // Obtener viajes activos para dashboard
    public function getActive()
    {
        $trips = Trip::active()
            ->withAllRelations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($trip) {
                return $trip->getSummaryInfo();
            });

        return response()->json($trips);
    }

    // Buscar viajes por vehículo o conductor
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->whereHas('vehicle', function($q) use ($query) {
                $q->where('plate_number', 'like', "%{$query}%");
            })
            ->orWhereHas('driver.user', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($trip) {
                return $trip->getSummaryInfo();
            });

        return response()->json($trips);
    }

    // Cancelar viaje
    public function cancelTrip(Trip $trip): RedirectResponse
    {
        if ($trip->cancel()) {
            return redirect()->route('trips.show', $trip)
                ->with('success', 'Viaje cancelado exitosamente.');
        }
        
        return redirect()->route('trips.index')
            ->with('error', 'No se pudo cancelar el viaje.');
    }
}
