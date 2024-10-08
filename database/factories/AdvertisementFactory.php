<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertisement>
 */
class AdvertisementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->realText(10),
            'owner' => $this->faker->name(),
            'photo' => $this->faker->imageUrl(),
            'start' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'end' => $this->faker->dateTimeBetween('+ 2 days', '+1 week')
        ];
    }
}
