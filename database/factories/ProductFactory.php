<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Color;
use App\Models\Category;
use App\Models\Size;
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
        $category = Category::inRandomOrder()->first();

        $sizeId = Size::where('category_id', $category->id)->inRandomOrder()->first();

        return [
            'name' => $this->faker->words(2, true),
            'color_id' => Color::inRandomOrder()->first()->id,
            'category_id' => $category->id, 
            'size_id' => $sizeId,
            'product_no' => $this->faker->unique()->date('YmdHis'),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $this->faker->imageUrl(100, 100, 'fashion'),
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Deleted']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
