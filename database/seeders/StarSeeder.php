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
        $faker = Faker::create();
        $starTypes = [
            'Yellow Dwarf', 
            'Red Giant', 
            'Blue Giant', 
            'White Dwarf', 
            'Neutron Star', 
            'Pulsar', 
            'Supergiant',
            'Red Supergiant',
            'Binary Star',
            'Brown Dwarf'
        ];

        $json = File::get(database_path('data\stars.json'));
        $stars = json_decode($json, true);
        Star::truncate();
        foreach ($stars as $item) {
            $star = new Star([
                'name' => $item,
                'type' => $starTypes[array_rand($starTypes)],
                'x' => $faker->randomFloat(2, -1000, 1000),
                'y' => $faker->randomFloat(2, -1000, 1000),
                'z' => $faker->randomFloat(2, -1000, 1000),
            ]);
            $star->save();
        }
    }
}
