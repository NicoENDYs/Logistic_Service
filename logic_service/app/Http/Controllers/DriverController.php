<?php
// app/Http/Controllers/DriverController.php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $drivers = Driver::with('user')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('license_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('drivers.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('driver')->get();
        return view('drivers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:drivers,user_id',
            'license_number' => 'required|string|max:50|unique:drivers,license_number',
            'phone' => 'required|string|max:30',
            'status' => 'required|in:activo,suspendido'
        ]);

        try {
            DB::beginTransaction();
            
            Driver::create($validated);
            
            DB::commit();
            
            return redirect()->route('drivers.index')
                ->with('success', 'Conductor creado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al crear el conductor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        $driver->load('user', 'trips.route', 'trips.vehicle');
        return view('drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        $users = User::all();
        return view('drivers.edit', compact('driver', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id', Rule::unique('drivers')->ignore($driver->id)],
            'license_number' => ['required', 'string', 'max:50', Rule::unique('drivers')->ignore($driver->id)],
            'phone' => 'required|string|max:30',
            'status' => 'required|in:activo,suspendido'
        ]);

        try {
            DB::beginTransaction();
            
            $driver->update($validated);
            
            DB::commit();
            
            return redirect()->route('drivers.index')
                ->with('success', 'Conductor actualizado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el conductor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        try {
            DB::beginTransaction();
            
            $driver->delete();
            
            DB::commit();
            
            return redirect()->route('drivers.index')
                ->with('success', 'Conductor eliminado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el conductor: ' . $e->getMessage());
        }
    }

    /**
     * Change driver status
     */
    public function changeStatus(Request $request, Driver $driver)
    {
        $request->validate([
            'status' => 'required|in:activo,suspendido'
        ]);

        try {
            $driver->update(['status' => $request->status]);
            
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
     * Generate drivers report
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $drivers = Driver::withCount(['trips' => function($query) use ($startDate, $endDate) {
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
        
        if ($request->has('export') && $request->export == 'pdf') {
            // Lógica para exportar a PDF
            return response()->json(['message' => 'Exportación a PDF']);
        }
        
        return view('drivers.report', compact('drivers'));
    }
}