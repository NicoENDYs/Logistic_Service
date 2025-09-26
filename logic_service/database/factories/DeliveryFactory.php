<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id'=> $this->faker->numberBetween(1,50),
            'customer_name' => $this->faker->name(),
            'delivery_adrress' => $this->faker->address(),
            'delivery_time' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement('pendiente', 'entregado', 'fallido')
        ];
    }
}
