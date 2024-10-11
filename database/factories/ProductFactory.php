<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => mt_rand(1, 4),
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(2, 1000, 100000),
            'stok' => mt_rand(1, 100),
        ];
    }
}
