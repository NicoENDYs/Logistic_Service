<?php

namespace App\Jobs;

use App\Models\Trip;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;


class SimulateGpsJob implements ShouldQueue
{
    use Queueable,InteractsWithQueue,SerializesModels;

    public $tripid;

    /**
     * Create a new job instance.
     */
    public function __construct(int $tripid)
    {
        $this->tripid= $tripid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            logger("🚀 Entrando al Job SimulateGps para trip {$this->tripid}");

        if (!config('app.simulation_mode')){
            logger("⛔ Simulación desactivada (config('app.simulation_mode') == false)");

            return;
        }
        
        // Fixed: Changed Find to find (proper case)
        $trip=Trip::find($this->tripid);
        if(! $trip) return;

        if ($trip->status !=='en_progreso') return;

        $last= $trip->locations()->latest('recorded_at')->first();
       if ($last && Carbon::parse($last->recorded_at)->gt(now()->subHours(1))){
             logger(" Ya hay una ubicacion hace menos de una hora en el trip{$trip->id} " );

    return;
}

        if($last){
            $deltaLat=(mt_rand(-50,50) / 1e5);
            $deltaLng=(mt_rand(-50,50) / 1e5);
            $lat = $last->latitude +$deltaLat;
            $lng= $last->longitude + $deltaLng;

        }else{
            $lat = $trip-> route_origin_lat ?? (4.65 + mt_rand(-100,100)/1e4);
            $lng = $trip->route_origin_lng ?? (4.65 + mt_rand(-100,100)/1e4);
        }
       logger("📍 Insertando posición simulada para viaje {$trip->id} ({$lat}, {$lng})");

    try {
        // Fixed: Changed rounding from 7 to 6 decimal places to match database schema
        $trip->locations()->create([
            'latitude' => round($lat,6),
            'longitude'=> round($lng,6),
            'recorded_at' => now(),
        ]);
        logger("✅ Registro insertado correctamente en BD para viaje {$trip->id}");
    } catch (\Exception $e) {
        logger("❌ Error al insertar: " . $e->getMessage());
        // Re-throw the exception so the job is marked as failed properly
        throw $e;
    }

    }
}