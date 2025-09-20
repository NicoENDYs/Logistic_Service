@extends('layouts.app')

@section('title', 'Gestión de Choferes')

@section('content')
<div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Choferes</h1>
            <a href="{{ route('drivers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-black px-5 py-2 rounded-lg shadow flex items-center transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Chofer
            </a>
        </div>


    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <form method="GET" action="{{ route('drivers.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/2">
                    <input type="text" name="search" placeholder="Buscar por nombre, email, licencia o teléfono" 
                           value="{{ request('search') }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full md:w-1/4">
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="suspendido" {{ request('status') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-black px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-search mr-2"></i> Buscar
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Licencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($drivers as $driver)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $driver->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $driver->user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $driver->license_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $driver->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $driver->status == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $driver->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('drivers.show', $driver) }}" 
                                class="inline-flex items-center bg-blue-500 text-black px-3 py-1 rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition">
                                    <i class="fas fa-eye mr-1"></i> Mostrar
                                </a>

                                <a href="{{ route('drivers.edit', $driver) }}" 
                                class="inline-flex items-center bg-yellow-500 text-black px-3 py-1 rounded-lg shadow hover:bg-yellow-600 hover:scale-105 transition">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </a>

                                <form action="{{ route('drivers.destroy', $driver) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center bg-red-500 text-black px-3 py-1 rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                            onclick="return confirm('¿Estás seguro de eliminar este conductor?')">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron conductores
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">
            {{ $drivers->links() }}
        </div>
    </div>
</div>
@endsection