<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate_number' => $this->bothify('???-####'),
            'brand' => $this->faker->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Chevrolet']),
            'model'  => $this->faker->numberBetween(2000, 2025),
            'capacity' => $this->faker->numberBetween(100, 1000),
            'status' => $this->faker->randomElement(['activo', 'mantenimiento', 'inactivo']),
        ];
    }
}
