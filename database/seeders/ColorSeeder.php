<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use App\Models\Color;

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

        $users = Admin::all();

        foreach ($users as $user) {
            foreach ($colors as $color) {
                Color::firstOrCreate([
                    'author_id' => $user->id,
                    'name' => $color,
                ]);
            }
        }
    }
}
