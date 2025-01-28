<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(2, true), 
            'color' => $this->faker->safeColorName(), 
            'category' => $this->faker->randomElement(['Jeans', 'Polo', 'Shirt', 'T-Shirt', 'Panjabi']),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL', null]), 
            'waist_size' => $this->faker->randomElement(['28', '30', '32', '34', '36', '38', null]),
            'panjabi_size' => $this->faker->randomElement(['28', '30', '32', '34', '36', '38', null]),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $this->faker->imageUrl(100, 100, 'fashion'), 
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Deleted']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
