<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Market;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'market_id'  => Market::inRandomOrder()->first()->id,
            'price'      => $this->faker->randomFloat(2, 1, 100),
            'recorded_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
