@extends('layouts.app')

@section('title', 'Detalles de la Ruta')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalles de la Ruta</h1>
        <div class="flex space-x-2">
            <a href="{{ route('routes.edit', $route) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <a href="{{ route('routes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Información de la Ruta</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Origen</p>
                    <p class="text-lg font-medium">{{ $route->origin }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Destino</p>
                    <p class="text-lg font-medium">{{ $route->destination }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Distancia</p>
                    <p class="text-lg font-medium">{{ $route->distance_km }} km</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tiempo estimado</p>
                    <p class="text-lg font-medium">{{ $route->estimated_time }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estado</p>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $route->status == 'activa' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $route->status == 'inactiva' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $route->status == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ $route->status }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fecha de registro</p>
                    <p class="text-lg font-medium">{{ $route->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($route->trips->count() > 0)
    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Viajes en esta Ruta ({{ $route->trips->count() }})</h2>
        </div>
        <div class="px-6 py-4">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($route->trips as $trip)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trip->driver->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trip->vehicle->brand }} {{ $trip->vehicle->model }} ({{ $trip->vehicle->plate_number }})</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trip->start_time ? $trip->start_time->format('d/m/Y H:i') : 'No iniciado' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $trip->status == 'completado' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $trip->status == 'en_progreso' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $trip->status == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $trip->status == 'cancelado' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $trip->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection