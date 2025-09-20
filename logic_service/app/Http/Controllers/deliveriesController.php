<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class deliveriesController extends Controller
{
    //mostrar las entregas en la bd
    public function index(): View
    {
        $deliveries = Delivery::with(['trip.vehicle', 'trip.driver.user', 'trip.route'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('deliveries.index', compact('deliveries'));
    }

    //mostrar formulario de creación de entrega
    public function create(): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->whereIn('status', ['pendiente', 'en_progreso'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('deliveries.create', compact('trips'));
    }

    //agregar una nueva entrega / insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_time' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['pendiente', 'entregado', 'fallido'])],
        ]);

        Delivery::create($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega creada exitosamente.');
    }

    //Mostrar una entrega
    public function show(Delivery $delivery): View
    {
        $delivery->load([
            'trip.vehicle', 
            'trip.driver.user', 
            'trip.route'
        ]);
        
        return view('deliveries.show', compact('delivery'));
    }

    //abrir formulario de editar entrega
    public function edit(Delivery $delivery): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->where('id', $delivery->trip_id)
            ->orWhereIn('status', ['pendiente', 'en_progreso'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('deliveries.edit', compact('delivery', 'trips'));
    }

    //Actualizar / editar entrega
    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_time' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['pendiente', 'entregado', 'fallido'])],
        ]);

        // Si se marca como entregado y no tiene fecha, asignar la actual
        if ($validated['status'] === 'entregado' && !$validated['delivery_time']) {
            $validated['delivery_time'] = now();
        }

        $delivery->update($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega actualizada exitosamente.');
    }

    //eliminar entrega
    public function destroy(Delivery $delivery): RedirectResponse
    {
        // Solo permitir eliminar entregas pendientes
        if ($delivery->status === 'entregado') {
            return redirect()->route('deliveries.index')
                ->with('error', 'No se puede eliminar una entrega ya completada.');
        }

        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega eliminada exitosamente.');
    }

    //cambiar estado de la entrega
    public function updateStatus(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pendiente', 'entregado', 'fallido'])],
        ]);

        $updateData = ['status' => $validated['status']];
        
        // Si se marca como entregado, registrar la fecha
        if ($validated['status'] === 'entregado' && !$delivery->delivery_time) {
            $updateData['delivery_time'] = now();
        }

        $delivery->update($updateData);

        return redirect()->route('deliveries.index')
            ->with('success', "Estado de la entrega cambiado a: {$validated['status']}");
    }

    // obtener entregas por estado via api
    public function getByStatus(string $status)
    {
        $deliveries = Delivery::with(['trip.vehicle', 'trip.driver.user'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'customer_name' => $delivery->customer_name,
                    'delivery_address' => $delivery->delivery_address,
                    'vehicle' => $delivery->trip->vehicle->plate_number ?? 'N/A',
                    'driver' => $delivery->trip->driver->user->name ?? 'N/A',
                    'status' => $delivery->status,
                    'delivery_time' => $delivery->delivery_time,
                ];
            });

        return response()->json($deliveries);
    }

    // obtener entregas de un viaje especifica
    public function getByTrip(Trip $trip)
    {
        $deliveries = $trip->deliveries()
            ->orderBy('created_at')
            ->get(['id', 'customer_name', 'delivery_address', 'status', 'delivery_time']);

        return response()->json($deliveries);
    }

    // marcar entrega como completada
    public function markAsDelivered(Delivery $delivery): RedirectResponse
    {
        if ($delivery->status === 'entregado') {
            return redirect()->route('deliveries.index')
                ->with('error', 'La entrega ya está marcada como entregada.');
        }

        $delivery->update([
            'status' => 'entregado',
            'delivery_time' => now(),
        ]);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Entrega marcada como completada exitosamente.');
    }

    // marcar entrega como fallida
    public function markAsFailed(Delivery $delivery): RedirectResponse
    {
        if ($delivery->status === 'fallido') {
            return redirect()->route('deliveries.index')
                ->with('error', 'La entrega ya está marcada como fallida.');
        }

        $delivery->update(['status' => 'fallido']);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Entrega marcada como fallida.');
    }
}
