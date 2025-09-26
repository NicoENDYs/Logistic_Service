<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Process\FakeProcessResult;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days= $this->faker->numberBetween(1, 7);
        $hours = $this->faker->numberBetween(1, 24);
        $totaltime=($days*24)+$hours;
        return [

            'origin' => $this->faker->randomElement('Medellin','Chocó','Cartago','Pereira','Cali','La tebaida','Bogota'),
            'destination' => $this->faker->randomElement('Medellin','Chocó','Cartago','Pereira','Cali','La tebaida','Bogota'),
            'distance_km'  => $this->faker->numberBetween(80, 1000),
            'estimated_time' => $this->faker->time('%02d:00:00', $totaltime),
            
        ];
    }
}
