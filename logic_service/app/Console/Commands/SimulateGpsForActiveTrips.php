<?php

namespace App\Console\Commands;

use App\Jobs\SimulateGpsJob;
use App\Models\Trip;
use Illuminate\Console\Command;

class SimulateGpsForActiveTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:gps';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula gps para todos los viajes en progreso';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trips = Trip::where('status', 'en_progreso')->get();

        foreach ($trips as $trip){
            dispatch((new SimulateGpsJob(($trip->id))));
        }

        $this->info('Dispatched job for '.$trips->count().' trips');
    }
}
