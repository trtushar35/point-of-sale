<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; 

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
            'Shirt', 
            'Half Sleeve Shirt', 
            'Full Sleeve Shirt',
            'Pant',
            'Gabardine ',
            'T-Shirt',
            'Panjabi',
            'Polo',
            'Jeans',
            'Jacket',
        ];

        foreach ($categories as $data) {
            Category::create([
                'name' => $data
            ]);
        }

    }
}
