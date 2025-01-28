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

    }
}
