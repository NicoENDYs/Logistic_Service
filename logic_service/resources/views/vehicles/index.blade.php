@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Vehículos</h1>
            <a href="{{ route('vehicles.create') }}"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow flex items-center transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Vehículo
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-4 border-b">
                <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Buscar por placa o marca"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                    </div>
                    <div class="w-full md:w-1/4">
                        <select name="status"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo
                            </option>
                            <option value="mantenimiento" {{ request('status') == 'mantenimiento' ? 'selected' : '' }}>
                                Mantenimiento</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                            <i class="fa-solid fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Capacidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($vehicles as $v)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $v->plate_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $v->brand }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $v->model }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($v->capacity) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $v->status === 'activo'
                                        ? 'bg-green-100 text-green-800'
                                        : ($v->status === 'mantenimiento'
                                            ? 'bg-amber-100 text-amber-800'
                                            : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($v->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('vehicles.edit', $v) }}"
                                            class="inline-flex items-center bg-yellow-500 text-white px-3 py-1 rounded-lg shadow hover:bg-yellow-600 hover:scale-105 transition">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>

                                        <form action="{{ route('vehicles.destroy', $v) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center bg-red-500 text-white px-3 py-1 rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                onclick="return confirm('¿Estás seguro de eliminar este vehículo?')">
                                                <i class="fas fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron vehículos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $vehicles->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
