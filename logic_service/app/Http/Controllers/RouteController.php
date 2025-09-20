<?php
// app/Http/Controllers/RouteController.php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $routes = Route::when($search, function ($query, $search) {
                return $query->where('origin', 'like', "%{$search}%")
                           ->orWhere('destination', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('routes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'estimated_time' => 'required|date_format:H:i',
            'status' => 'required|in:activa,inactiva,pendiente'
        ]);

        try {
            DB::beginTransaction();
            
            Route::create($validated);
            
            DB::commit();
            
            return redirect()->route('routes.index')
                ->with('success', 'Ruta creada exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al crear la ruta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        $route->load('trips.driver.user', 'trips.vehicle');
        return view('routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        return view('routes.edit', compact('route'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'estimated_time' => 'required|date_format:H:i',
            'status' => 'required|in:activa,inactiva,pendiente'
        ]);

        try {
            DB::beginTransaction();
            
            $route->update($validated);
            
            DB::commit();
            
            return redirect()->route('routes.index')
                ->with('success', 'Ruta actualizada exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la ruta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        try {
            DB::beginTransaction();
            
            // Verificar si la ruta tiene viajes asociados
            if ($route->trips()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la ruta porque tiene viajes asociados.');
            }
            
            $route->delete();
            
            DB::commit();
            
            return redirect()->route('routes.index')
                ->with('success', 'Ruta eliminada exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar la ruta: ' . $e->getMessage());
        }
    }

    /**
     * Change route status
     */
    public function changeStatus(Request $request, Route $route)
    {
        $request->validate([
            'status' => 'required|in:activa,inactiva,pendiente'
        ]);

        try {
            $route->update(['status' => $request->status]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Estado actualizado correctamente',
                'new_status' => $request->status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate routes report
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $routes = Route::withCount(['trips' => function($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }])
        ->with(['trips' => function($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }])
        ->orderBy('trips_count', 'desc')
        ->get();
        
        return view('routes.report', compact('routes'));
    }
}