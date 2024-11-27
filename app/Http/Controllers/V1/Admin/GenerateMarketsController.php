<?php

namespace App\Http\Controllers\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Resource, Planet, Market};

class GenerateMarketsController extends Controller
{
    public function __invoke()
    {
        $planets = Planet::where('type', 'Terrestrial Planet')->get();
        $resources = Resource::all();
        $markets = [];
        //Market::truncate();
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
                    $market['base_production'] = rand(10, $consume);
                }
                $markets[] = $market;
            }
        }
        foreach(array_chunk($markets, 100) as $chunk) {
            Market::insert($chunk);
        }

        return Market::all();
    }
}