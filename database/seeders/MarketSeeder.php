<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\{Resource, Market, Planet};

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planets = Planet::all();
        $resources = Resource::all();
        $markets = [];
        Market::truncate();
        foreach ($planets as $planet) {
            foreach ($resources as $resource) {
                $consume = $planet->population/100000*$resource->rate_per_100k_population;
                $productionValue = ceil(rand(0.1, 1.2)*$consume);
                $market = [
                    "planet_id" => $planet->id,
                    "resource_id" => $resource->id,
                    "stock" => rand(100, 1000),
                    "base_production" => $productionValue,
                ];
                if($market['base_production'] == 0) {
                    $market['base_production'] = floor(rand(10, $consume*1.50));
                }
                $markets[] = $market;
            }
        }
        foreach(array_chunk($markets, 100) as $chunk) {
            Market::insert($chunk);
        }
    }
}
