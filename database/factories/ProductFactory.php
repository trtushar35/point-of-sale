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
        $categories = [
            'Shirt' => ['S', 'M', 'L', 'XL'],
            'Pant' => ['30', '32', '34'],
            'Jeans' => ['30', '32', '34', '36'],
            'T-Shirt' => ['S', 'M', 'L', 'XL'],
            'Polo' => ['S', 'M', 'L', 'XL'],
            'Full Sleeve Shirt' => ['S', 'M', 'L', 'XL'],
            'Half Sleeve Shirt' => ['S', 'M', 'L', 'XL'],
        ];

        foreach ($categories as $categoryName => $sizes) {
            $category = Category::create(['name' => $categoryName]);

            foreach ($sizes as $size) {
                Size::create([
                    'category_id' => $category->id,
                    'size' => $size
                ]);
            }
        }

        return [
            'name' => $this->faker->words(2, true),
            'color_id' => Color::inRandomOrder()->first()->id,
            'category_id' => $category->id, // Use the found category id
            'size_id' => $size->id, // Ensure the size id is not null
            'product_no' => $this->faker->unique()->numerify('P######'),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $this->faker->imageUrl(100, 100, 'fashion'),
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Deleted']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
