<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\{Star, Planet};

class PlanetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Planet::truncate();
        $planets = json_decode(File::get(database_path('data/planets.json')), true);
        foreach ($planets as $item) {
            $star = Star::where('name', $item['star_name'])->first();
            $planet = new Planet([
                'name' => $item['name'],
                'star_id' => $star->id,
                'distance_from_star' => $item['distance_from_star'],
                'type' => $item['type'],
                'population' => rand(100000, 10000000000),
            ]);
            $planet->save();
        }
    }
}
