<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Models\TripLocation;

class TripLocationController extends Controller
{

   public function getAddressFromCoordinates($lat, $lng)
{
    $response = Http::withHeaders([
        'User-Agent' => 'TuAppLaravel/1.0'
    ])->get('https://nominatim.openstreetmap.org/reverse', [
        'format' => 'json',
        'lat' => $lat,
        'lon' => $lng,
        'zoom' => 10,
        'addressdetails' => 1,
    ]);

    if ($response->successful()) {
        $data = $response->json();
        return $data['address']['city']
            ?? $data['address']['town']
            ?? $data['address']['village']
            ?? $data['display_name']
      
            ?? 'Desconocido';
    }

    return 'Desconocido';
}

    public function showLocations($tripId){
    $locations = TripLocation::where('trip_id', $tripId)->get();

    foreach ($locations as $loc) {
        $loc->city = $this->getAddressFromCoordinates($loc->latitude, $loc->longitude);
    }

    return view('trips.locations', compact('locations'));
    }


    public function store(Request $Request, $tripid ){
        $trip = Trip::findOrFail($tripid);

        $data=$Request->Validate([
            'latitude'=> 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $trip->locations()->create([
            ...$data,
            'recorded_at' => now(),
        ]);

        return redirect()->route(('trips.show'),$tripid)
                        ->with('success','ubicacion registrada');

    }
}
