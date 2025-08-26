@extends('layouts.app')

@section('content')
    <x-card class="mb-4">
        <div class="flex items-center justify-between gap-3">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por placa o marca"
                    class="w-64 rounded-lg border-gray-300" />
                <button class="px-3 py-2 rounded-lg bg-gray-900 text-white">Buscar</button>
            </form>
            <a href="{{ route('vehicles.create') }}" class="px-3 py-2 rounded-lg bg-green-600 text-white">Nuevo</a>
        </div>
    </x-card>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($vehicles as $v)
                        <tr>
                            <td class="py-2 font-medium">{{ $v->plate_number }}</td>
                            <td>{{ $v->brand }}</td>
                            <td>{{ $v->model }}</td>
                            <td>{{ number_format($v->capacity) }}</td>
                            <td><span
                                    class="px-2 py-0.5 rounded-full text-xs
                        {{ $v->status === 'activo'
                            ? 'bg-green-100 text-green-700'
                            : ($v->status === 'mantenimiento'
                                ? 'bg-amber-100 text-amber-700'
                                : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($v->status) }}</span>
                            </td>
                            <td class="text-right">
                                <a class="text-blue-600 hover:underline" href="{{ route('vehicles.edit', $v) }}">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $vehicles->withQueryString()->links() }}</div>
    </x-card>
@endsection
