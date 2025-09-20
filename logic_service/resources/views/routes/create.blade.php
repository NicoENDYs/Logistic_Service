@extends('layouts.app')

@section('title', 'Crear Nueva Ruta')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Crear Nueva Ruta</h1>
        <a href="{{ route('routes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('routes.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">Origen *</label>
                    <input type="text" name="origin" id="origin" value="{{ old('origin') }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('origin') border-red-500 @enderror" 
                           placeholder="Ej: Ciudad de México" required>
                    @error('origin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">Destino *</label>
                    <input type="text" name="destination" id="destination" value="{{ old('destination') }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination') border-red-500 @enderror" 
                           placeholder="Ej: Guadalajara" required>
                    @error('destination')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="distance_km" class="block text-sm font-medium text-gray-700 mb-2">Distancia (km) *</label>
                    <input type="number" step="0.01" name="distance_km" id="distance_km" value="{{ old('distance_km') }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('distance_km') border-red-500 @enderror" 
                           placeholder="Ej: 534.5" min="0" required>
                    @error('distance_km')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_time" class="block text-sm font-medium text-gray-700 mb-2">Tiempo Estimado *</label>
                    <input type="time" name="estimated_time" id="estimated_time" value="{{ old('estimated_time') }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('estimated_time') border-red-500 @enderror" 
                           required>
                    @error('estimated_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                        <option value="">Seleccione un estado</option>
                        <option value="activa" {{ old('status') == 'activa' ? 'selected' : '' }}>Activa</option>
                        <option value="inactiva" {{ old('status') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                        <option value="pendiente" {{ old('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                    <i class="fas fa-save mr-2"></i> Guardar Ruta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection