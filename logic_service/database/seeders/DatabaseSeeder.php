<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder; // 👈 singular
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DriverSeeder::class,
            VehicleSeeder::class,
            RouteSeeder::class,
            TripSeeder::class,
            DeliverySeeder::class,
            IncidentSeeder::class,
        ]);
    }
}
