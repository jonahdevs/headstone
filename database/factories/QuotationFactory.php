<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Memorial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quotation>
 */
class QuotationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dimensions = fake()->numberBetween(30, 100) . 'cm x ' .
            fake()->numberBetween(20, 80) . 'cm x ' .
            fake()->numberBetween(5, 30) . 'cm';
        return [
            'customer_id' => User::customers()->inRandomOrder()->first()->id,
            'memorial_id' => Memorial::inRandomOrder()->first()->id,
            'material_id' => Material::inRandomOrder()->first()->id,
            'budget' => fake()->numberBetween(10000, 50000),
            'dimensions' => $dimensions,
        ];
    }
}
