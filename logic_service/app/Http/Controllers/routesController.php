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
        if ($route->trips()->whereIn('status', ['pendiente', 'en_progreso'])->exists()) {
            return redirect()->route('routes.index')
                ->with('error', 'No se puede eliminar una ruta con viajes activos.');
        }

        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Ruta eliminada exitosamente.');
    }

    // obtener rutas por api
    public function getRoutes()
    {
        $routes = Route::orderBy('origin')
            ->get(['id', 'origin', 'destination', 'distance_km', 'estimated_time'])
            ->map(function ($route) {
                return [
                    'id' => $route->id,
                    'description' => $route->description,
                    'origin' => $route->origin,
                    'destination' => $route->destination,
                    'distance_km' => $route->distance_km,
                    'estimated_time' => $route->estimated_time,
                ];
            });

        return response()->json($routes);
    }

    // calcular distancia estimada entre dos rutas
    public function calculateDistance(Request $request)
    {
        $validated = $request->validate([
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
        ]);

        // Aquí se puede integrar con una API de mapas como Google Maps
        // Por ahora retornamos un cálculo básico
        $estimatedDistance = rand(10, 500); // Simulación
        $estimatedTime = gmdate('H:i', ($estimatedDistance / 60) * 3600); // Asume 60 km/h

        return response()->json([
            'distance_km' => $estimatedDistance,
            'estimated_time' => $estimatedTime,
        ]);
    }
}
