<?php

namespace App\Http\Controllers;

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

    //mostrar formulario de creación de incidente
    public function create(): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->whereIn('status', ['en_progreso', 'completado'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('incidents.create', compact('trips'));
    }

    //agregar un nuevo incidente / insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'description' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'string', Rule::in(['accidente', 'retraso', 'mecanico', 'otro'])],
            'reported_at' => ['nullable', 'date'],
            'resolved' => ['boolean'],
        ]);

        // Si no se especifica fecha, usar la actual
        if (!isset($validated['reported_at'])) {
            $validated['reported_at'] = now();
        }

        $validated['resolved'] = $validated['resolved'] ?? false;

        Incident::create($validated);

        return redirect()->route('incidents.index')
            ->with('success', 'Incidente registrado exitosamente.');
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
            
        return view('incidents.edit', compact('incident', 'trips'));
    }

    //Actualizar / editar incidente
    public function update(Request $request, Incident $incident): RedirectResponse
    {
        $validated = $request->validate([
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'description' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'string', Rule::in(['accidente', 'retraso', 'mecanico', 'otro'])],
            'reported_at' => ['nullable', 'date'],
            'resolved' => ['boolean'],
        ]);

        $incident->update($validated);

        return redirect()->route('incidents.index')
            ->with('success', 'Incidente actualizado exitosamente.');
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
        if ($incident->resolved) {
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
        $newStatus = !$incident->resolved;
        $incident->update(['resolved' => $newStatus]);

        $message = $newStatus ? 'resuelto' : 'pendiente';
        return redirect()->route('incidents.index')
            ->with('success', "Incidente marcado como: {$message}");
    }

    // obtener incidentes por estado via api
    public function getByStatus(string $resolved)
    {
        $isResolved = $resolved === 'resolved' ? true : false;
        
        $incidents = Incident::with(['trip.vehicle', 'trip.driver.user'])
            ->where('resolved', $isResolved)
            ->orderBy('reported_at', 'desc')
            ->get()
            ->map(function ($incident) {
                return [
                    'id' => $incident->id,
                    'description' => substr($incident->description, 0, 100) . '...',
                    'type' => $incident->type,
                    'vehicle' => $incident->trip->vehicle->plate_number ?? 'N/A',
                    'driver' => $incident->trip->driver->user->name ?? 'N/A',
                    'reported_at' => $incident->reported_at,
                    'resolved' => $incident->resolved,
                ];
            });

        return response()->json($incidents);
    }

    // obtener incidentes de un viaje especifica
    public function getByTrip(Trip $trip)
    {
        $incidents = $trip->incidents()
            ->orderBy('reported_at', 'desc')
            ->get(['id', 'description', 'type', 'reported_at', 'resolved']);

        return response()->json($incidents);
    }

    // obtener incidentes por tipo
    public function getByType(string $type)
    {
        $incidents = Incident::with(['trip.vehicle', 'trip.driver.user'])
            ->where('type', $type)
            ->orderBy('reported_at', 'desc')
            ->get()
            ->map(function ($incident) {
                return [
                    'id' => $incident->id,
                    'description' => substr($incident->description, 0, 100) . '...',
                    'vehicle' => $incident->trip->vehicle->plate_number ?? 'N/A',
                    'driver' => $incident->trip->driver->user->name ?? 'N/A',
                    'reported_at' => $incident->reported_at,
                    'resolved' => $incident->resolved,
                ];
            });

        return response()->json($incidents);
    }

    // obtener resumen de incidentes
    public function getSummary()
    {
        $summary = [
            'total' => Incident::count(),
            'resolved' => Incident::where('resolved', true)->count(),
            'pending' => Incident::where('resolved', false)->count(),
            'by_type' => [
                'accidente' => Incident::where('type', 'accidente')->count(),
                'retraso' => Incident::where('type', 'retraso')->count(),
                'mecanico' => Incident::where('type', 'mecanico')->count(),
                'otro' => Incident::where('type', 'otro')->count(),
            ]
        ];

        return response()->json($summary);
    }
}
