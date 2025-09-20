@extends('layouts.app')
@section('content')
<x-card>
    <h1 class="text-xl font-semibold mb-4">Editar vehículo</h1>
    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">
        @method('PUT')
        @include('vehicles.form', ['vehicle'=>$vehicle])
    </form>
</x-card>
@endsection
