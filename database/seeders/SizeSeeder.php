<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Models\Size; 

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Shirt' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
            'Jeans' => ['28', '29', '30', '31', '32', '33','34', '35', '36', '37', '38'],
            'T-Shirt' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
            'Polo' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
            'Full Shirt' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
            'Half Shirt' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
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

    }
}
