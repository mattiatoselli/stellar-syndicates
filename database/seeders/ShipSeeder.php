<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\{Ship};

class ShipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ship::truncate();
        $ships = json_decode(File::get(database_path('data/ships.json')), true);
        Ship::insert($ships);
    }
}
