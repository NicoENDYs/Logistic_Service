@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Editar Vehículo</h1>
            <a href="{{ route('vehicles.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow flex items-center transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">
                    @method('PUT')
                    @include('vehicles.form', ['vehicle' => $vehicle])
                </form>
            </div>
        </div>
    </div>
@endsection
