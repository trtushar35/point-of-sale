<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color; // Assuming the Color model exists

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define a list of color names
        $colors = [
            'Red',
            'Blue',
            'Green',
            'Yellow',
            'Black',
            'White',
            'Gray',
            'Pink',
            'Purple',
            'Orange',
            'Brown',
            'Beige',
            'Turquoise',
            'Cyan',
            'Magenta',
            'Lime',
            'Lavender',
            'Violet'
        ];

        // Insert each color into the colors table
        foreach ($colors as $color) {
            Color::create([
                'name' => $color
            ]);
        }

        // Alternatively, if you prefer using the DB facade for bulk insertion:
        // DB::table('colors')->insert(array_map(fn($color) => ['name' => $color], $colors));
    }
}
