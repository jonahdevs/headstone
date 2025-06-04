<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Memorial>
 */
class MemorialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(20000, 80000);
        $sale_price = fake()->optional(30)->numberBetween($price * 75 / 100, $price * 90 / 100);
        return [
            'status' => 'published',
            'price' => $price,
            'sale_price' => $sale_price,
            'stock' => 1,
            'created_by' => User::operators()->inRandomOrder()->first()->id,
            'estimated_delivery_time' => fake()->randomElement(['one_week', 'two_weeks', 'one_month', 'one_month+']),
        ];
    }
}
