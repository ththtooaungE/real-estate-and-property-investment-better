<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $townships = [
            'Amarapura',
            'Aungmyethazan',
            'Chanayethazan',
            'Chanmyathazi',
            'Maha Aungmye',
            'Patheingyi',
            'Pyigyidagun',
        ];

        $widths = ['15 feet', '20 feet', '30 feet', '60 feet', '100 feet'];
        $lengths = ['20 feet', '40 feet', '60 feet', '80 feet', '100 feet'];
        $status = ['sell', 'rent'];
        // $price = []

        return [
            'user_id' => User::factory()->create(['is_agent' => true]),
            'description' => $this->faker->realText(200),
            'street' => $this->faker->streetAddress(),
            'township' => $townships[rand(0, 6)],
            'city' => 'Mandalay',
            'state_or_division' => 'Mandalay',
            'price' => 100000,
            'width' => $widths[rand(0, 4)],
            'length' => $lengths[rand(0, 4)],
            'status' => $status[rand(0, 1)],
            // 'price' => 
        ];
    }
}
