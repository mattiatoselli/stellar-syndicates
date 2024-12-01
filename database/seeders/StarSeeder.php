<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\Star;

class StarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Star::truncate();
        $json = File::get(database_path('data\stars.json'));
        $stars = json_decode($json, true);
        foreach ($stars as $item) {
            $star = new Star([
                'name' => $item['name'],
                'spectrum' => $item['spectrum'],
                'color' => $item['color'],
                'temperature' => $item['temperature'],
                'x' => $item['position']['x'],
                'y' => $item['position']['y'],
                'z' => $item['position']['z'],
            ]);
            $star->save();
        }
    }
}
