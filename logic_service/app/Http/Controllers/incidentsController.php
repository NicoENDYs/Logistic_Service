<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Incident;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class incidentsController extends Controller
{
    //mostrar los incidentes en la bd
    public function index(): View
    {
        $incidents = Incident::with(['trip.vehicle', 'trip.driver.user', 'trip.route'])
            ->orderBy('reported_at', 'desc')
            ->paginate(10);
        
        return view('incidents.index', compact('incidents'));
    }

      //agregar un nuevo incidente / insertar
    public function store(StoreIncidentRequest $request)
    {
        Incident::create($request->validated());
        return redirect()->route('drivers.index')
                         ->with('success', 'Driver created successfully');
    }

    //Actualizar / editar incidente
    public function update(UpdateIncidentRequest $request, Incident $driver)
    {
        $driver->update($request->validated());
        return redirect()->route('drivers.index')
                         ->with('success', 'Driver updated successfully');
    }

    //mostrar formulario de creación de incidente
    public function create(): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->whereIn('status', ['en_progreso', 'completado'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $types = Incident::getTypes();
        
        return view('incidents.create', compact('trips', 'types'));
    }

    //Mostrar un incidente
    public function show(Incident $incident): View
    {
        $incident->load([
            'trip.vehicle', 
            'trip.driver.user', 
            'trip.route',
            'trip.deliveries'
        ]);
        
        return view('incidents.show', compact('incident'));
    }

    //abrir formulario de editar incidente
    public function edit(Incident $incident): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->where('id', $incident->trip_id)
            ->orWhereIn('status', ['en_progreso', 'completado'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $types = Incident::getTypes();
            
        return view('incidents.edit', compact('incident', 'trips', 'types'));
    }

    //eliminar incidente
    public function destroy(Incident $incident): RedirectResponse
    {
        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incidente eliminado exitosamente.');
    }

    //marcar incidente como resuelto
    public function markAsResolved(Incident $incident): RedirectResponse
    {
        if ($incident->isResolved()) {
            return redirect()->route('incidents.index')
                ->with('error', 'El incidente ya está marcado como resuelto.');
        }

        $incident->markAsResolved();

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incidente marcado como resuelto exitosamente.');
    }

    //cambiar estado de resolución del incidente
    public function toggleResolved(Incident $incident): RedirectResponse
    {
        $newStatus = $incident->toggleResolved();
        $message = $newStatus ? 'resuelto' : 'pendiente';
        
        return redirect()->route('incidents.index')
            ->with('success', "Incidente marcado como: {$message}");
    }

    // obtener resumen de incidentes
    public function getSummary(): RedirectResponse
    {
        $summary = Incident::getStatsSummary();
        
        return redirect()->back()
            ->with('summary', $summary)
            ->with('success', 'Resumen de incidentes cargado exitosamente.');
    }

    // Obtener incidentes pendientes para dashboard
    public function getPending()
    {
        $incidents = Incident::pending()
            ->withRelations()
            ->orderBy('reported_at', 'desc')
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }

    // Buscar incidentes por descripción o tipo
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $incidents = Incident::with(['trip.vehicle', 'trip.driver.user'])
            ->where('description', 'like', "%{$query}%")
            ->orWhere('type', 'like', "%{$query}%")
            ->orderBy('reported_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }

    // Obtener incidentes recientes
    public function getRecent()
    {
        $incidents = Incident::recent()
            ->withRelations()
            ->orderBy('reported_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }

    // Obtener incidentes por tipo
    public function getByType(string $type)
    {
        $incidents = Incident::byType($type)
            ->withRelations()
            ->orderBy('reported_at', 'desc')
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }
}
