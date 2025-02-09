<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Size;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($sizes as $size) {
                Size::firstOrCreate([
                    'category_id' => $category->id,
                    'size' => $size,
                ]);
            }
        }
    }
}
