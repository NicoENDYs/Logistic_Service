<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class vehiclesController extends Controller
{

    //mostrar los vehiculos en la bd
    public function index(): View
    {
        $vehicles = Vehicle::orderBy('created_at', 'desc')->paginate(10);
        
        return view('vehicles.index', compact('vehicles'));
    }

    //mostrar formulario de creacion de vehiculo
    public function create(): View
    {
        $statuses = Vehicle::getStatuses();
        return view('vehicles.create', compact('statuses'));
    }

    //agregar un nuevo vehiculo /insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'status' => ['required', 'string', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
        ]);

        try {
            Vehicle::create($validated);
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehículo creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el vehículo: ' . $e->getMessage());
        }
    }

    //Mostrar un vehículo
    public function show(Vehicle $vehicle): View
    {
        return view('vehicles.show', compact('vehicle'));
    }

    //abrir formulario de editar vehiculo
    public function edit(Vehicle $vehicle): View
    {
        $statuses = Vehicle::getStatuses();
        return view('vehicles.edit', compact('vehicle', 'statuses'));
    }

    //Actualizar /editarvehiculo
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'plate_number' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)
            ],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'status' => ['required', 'string', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    //eliminar vehiculo
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }

    //cambiar estdo del vehiculo
    public function toggleStatus(Vehicle $vehicle): RedirectResponse
    {
        $newStatus = $vehicle->toggleStatus();

        return redirect()->route('vehicles.index')
            ->with('success', "Estado del vehículo cambiado a: {$newStatus}");
    }

    // Obtener vehículos disponibles para AJAX/select2
    public function getAvailable()
    {
        $vehicles = Vehicle::available()
            ->orderBy('plate_number')
            ->get()
            ->map(function($vehicle) {
                return $vehicle->getSummaryInfo();
            });

        return response()->json($vehicles);
    }

    // Buscar vehículos por placa
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $vehicles = Vehicle::where('plate_number', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->orWhere('model', 'like', "%{$query}%")
            ->orderBy('plate_number')
            ->take(10)
            ->get()
            ->map(function($vehicle) {
                return $vehicle->getSummaryInfo();
            });

        return response()->json($vehicles);
    }
}
