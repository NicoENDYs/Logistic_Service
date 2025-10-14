@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Últimas posiciones del viaje {{ $trip->name ?? '' }}</h1>

    {{-- Tabla con posiciones --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Latitud</th>
                <th>Longitud</th>
                <th>Fecha/Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $loc)
                <tr>
                    <td>{{ $loc->latitude }}</td>
                    <td>{{ $loc->longitude }}</td>
                    <td>{{ $loc->recorded_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Mapa --}}
    <h2>Mapa de ubicaciones</h2>
    <div id="map" style="height: 500px; width: 100%;"></div>
</div>

{{-- Leaflet JS + CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Traemos todas las ubicaciones como JSON desde Laravel
    var locations = @json($locations);

    // Inicializamos el mapa
    var map;

    if (locations.length > 0) {
        // Centramos en la primera ubicación
        var firstLng = locations[0].longitude;
        map = L.map('map').setView([firstLat, firstLng], 12);
    } else {
        // Bogotá por defecto
        map = L.map('map').setView([4.6097, -74.0817], 12);
    }

    // Capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Agregamos todos los marcadores
    locations.forEach(function(loc) {
        L.marker([loc.latitude, loc.longitude])
            .addTo(map)
            .bindPopup("Fecha: " + loc.recorded_at + "<br>Lat: " + loc.latitude + "<br>Lng: " + loc.longitude);
    });
});
</script>

@endsection
