@extends('layouts.app')
@section('content')
<x-card>
    <h1 class="text-xl font-semibold mb-4">Nuevo vehículo</h1>
    <form method="POST" action="{{ route('vehicles.store') }}">
        @include('vehicles.form')
    </form>
</x-card>
@endsection
