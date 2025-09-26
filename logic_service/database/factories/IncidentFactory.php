<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => $this->faker->numberBetween(1, 50),
            'description'  => $this->faker->text,
            'type' => $this->faker->randomElement(['accidente', 'retraso', 'mecanico', 'otro']),
            'reported_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'resolved' => $this->faker->numberBetween(0, 1),
        ];
    }
}
