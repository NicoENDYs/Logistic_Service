<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class routesController extends Controller
{
    //mostrar las rutas en la bd
    public function index(): View
    {
        $routes = Route::orderBy('created_at', 'desc')->paginate(10);
        
        return view('routes.index', compact('routes'));
    }

    //mostrar formulario de creación de ruta
    public function create(): View
    {
        return view('routes.create');
    }

    //agregar una nueva ruta / insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'distance_km' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'estimated_time' => ['nullable', 'date_format:H:i'],
        ]);

        Route::create($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Ruta creada exitosamente.');
    }

    //Mostrar una ruta
    public function show(Route $route): View
    {
        $route->load('trips.vehicle', 'trips.driver.user');
        
        return view('routes.show', compact('route'));
    }

    //abrir formulario de editar ruta
    public function edit(Route $route): View
    {
        return view('routes.edit', compact('route'));
    }

    //Actualizar / editar ruta
    public function update(Request $request, Route $route): RedirectResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'distance_km' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'estimated_time' => ['nullable', 'date_format:H:i'],
        ]);

        $route->update($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Ruta actualizada exitosamente.');
    }

    //eliminar ruta
    public function destroy(Route $route): RedirectResponse
    {
        // Verificar si tiene viajes activos
        if (!$route->canBeDeleted()) {
            return redirect()->route('routes.index')
                ->with('error', 'No se puede eliminar una ruta con viajes activos.');
        }

        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Ruta eliminada exitosamente.');
    }

    // Calcular distancia entre origen y destino
    public function calculateDistance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
        ]);

        $calculation = Route::calculateEstimatedDistance(
            $validated['origin'], 
            $validated['destination']
        );

        return redirect()->back()
            ->with('calculation', $calculation)
            ->with('success', 'Distancia calculada exitosamente.');
    }

    // Obtener rutas disponibles para AJAX/select2
    public function getAvailable()
    {
        $routes = Route::available()
            ->orderBy('origin')
            ->get()
            ->map(function($route) {
                return $route->getSummaryInfo();
            });

        return response()->json($routes);
    }

    // Buscar rutas por origen o destino
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $routes = Route::where('origin', 'like', "%{$query}%")
            ->orWhere('destination', 'like', "%{$query}%")
            ->orderBy('origin')
            ->take(10)
            ->get()
            ->map(function($route) {
                return $route->getSummaryInfo();
            });

        return response()->json($routes);
    }
}
