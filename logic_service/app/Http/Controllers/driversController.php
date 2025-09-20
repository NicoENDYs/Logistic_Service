<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class driversController extends Controller
{
    //mostrar los conductores en la bd
    public function index(): View
    {
        $drivers = Driver::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('drivers.index', compact('drivers'));
    }

    //mostrar formulario de creación de conductor
    public function create(): View
    {
        $users = User::whereDoesntHave('driver')
            ->where('role_id', '!=', 1) // No incluir administradores
            ->orderBy('name')
            ->get();
            
        $statuses = Driver::getStatuses();
            
        return view('drivers.create', compact('users', 'statuses'));
    }

    //agregar un nuevo conductor / insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:drivers,user_id'],
            'license_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'status' => ['required', 'string', Rule::in(['activo', 'suspendido'])],
        ]);

        Driver::create($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Conductor creado exitosamente.');
    }

    //Mostrar un conductor
    public function show(Driver $driver): View
    {
        $driver->load('user', 'trips.vehicle', 'trips.route');
        
        return view('drivers.show', compact('driver'));
    }

    //abrir formulario de editar conductor
    public function edit(Driver $driver): View
    {
        $users = User::where('id', $driver->user_id)
            ->orWhereDoesntHave('driver')
            ->where('role_id', '!=', 1)
            ->orderBy('name')
            ->get();
            
        $statuses = Driver::getStatuses();
            
        return view('drivers.edit', compact('driver', 'users', 'statuses'));
    }

    //Actualizar / editar conductor
    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => [
                'required', 
                'integer', 
                'exists:users,id', 
                Rule::unique('drivers', 'user_id')->ignore($driver->id)
            ],
            'license_number' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'status' => ['required', 'string', Rule::in(['activo', 'suspendido'])],
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Conductor actualizado exitosamente.');
    }

    //eliminar conductor
    public function destroy(Driver $driver): RedirectResponse
    {
        // Verificar si tiene viajes activos
        if ($driver->trips()->whereIn('status', ['pendiente', 'en_progreso'])->exists()) {
            return redirect()->route('drivers.index')
                ->with('error', 'No se puede eliminar un conductor con viajes activos.');
        }

        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Conductor eliminado exitosamente.');
    }

    //cambiar estado del conductor
    public function toggleStatus(Driver $driver): RedirectResponse
    {
        $newStatus = $driver->toggleStatus();

        return redirect()->route('drivers.index')
            ->with('success', "Estado del conductor cambiado a: {$newStatus}");
    }

    // Obtener conductores disponibles para AJAX/select2
    public function getAvailable()
    {
        $drivers = Driver::available()
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->map(function($driver) {
                return $driver->getSummaryInfo();
            });

        return response()->json($drivers);
    }

    // Buscar conductores por nombre o licencia
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $drivers = Driver::with('user')
            ->where('license_number', 'like', "%{$query}%")
            ->orWhereHas('user', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orderBy('created_at')
            ->take(10)
            ->get()
            ->map(function($driver) {
                return $driver->getSummaryInfo();
            });

        return response()->json($drivers);
    }
}
