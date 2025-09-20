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
        
        $statuses = Delivery::getStatuses();
        
        return view('deliveries.create', compact('trips', 'statuses'));
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
            
        $statuses = Delivery::getStatuses();
            
        return view('deliveries.edit', compact('delivery', 'trips', 'statuses'));
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

        $delivery->changeStatus($validated['status']);
        $delivery->update(collect($validated)->except('status')->toArray());
        
        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega actualizada exitosamente.');
    }

    //eliminar entrega
    public function destroy(Delivery $delivery): RedirectResponse
    {
        // Solo permitir eliminar entregas pendientes
        if (!$delivery->canBeDeleted()) {
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

        if ($delivery->changeStatus($validated['status'])) {
            return redirect()->route('deliveries.index')
                ->with('success', "Estado de la entrega cambiado a: {$validated['status']}");
        }
        
        return redirect()->back()
            ->with('error', 'No se pudo cambiar el estado de la entrega.');
    }

    // marcar entrega como completada
    public function markAsDelivered(Delivery $delivery): RedirectResponse
    {
        if ($delivery->markAsDelivered()) {
            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Entrega marcada como completada exitosamente.');
        }
        
        return redirect()->route('deliveries.index')
            ->with('error', 'La entrega ya está marcada como entregada.');
    }

    // marcar entrega como fallida
    public function markAsFailed(Delivery $delivery): RedirectResponse
    {
        if ($delivery->markAsFailed()) {
            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Entrega marcada como fallida.');
        }
        
        return redirect()->route('deliveries.index')
            ->with('error', 'La entrega ya está marcada como fallida.');
    }

    // Obtener entregas pendientes para dashboard
    public function getPending()
    {
        $deliveries = Delivery::byStatus(Delivery::STATUS_PENDING)
            ->withRelations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($delivery) {
                return $delivery->getSummaryInfo();
            });

        return response()->json($deliveries);
    }

    // Buscar entregas por cliente o dirección
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $deliveries = Delivery::with(['trip.vehicle', 'trip.driver.user'])
            ->where('customer_name', 'like', "%{$query}%")
            ->orWhere('delivery_address', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($delivery) {
                return $delivery->getSummaryInfo();
            });

        return response()->json($deliveries);
    }

    // Obtener entregas de hoy
    public function getToday()
    {
        $deliveries = Delivery::whereDate('created_at', today())
            ->withRelations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($delivery) {
                return $delivery->getSummaryInfo();
            });

        return response()->json($deliveries);
    }
}
