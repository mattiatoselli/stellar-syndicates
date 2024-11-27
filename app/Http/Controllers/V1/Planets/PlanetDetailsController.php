<?php

namespace App\Http\Controllers\V1\Planets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Planet, Resource};
use Illuminate\Support\Facades\File;

class PlanetDetailsController extends Controller
{
    public function __invoke(string $id)
    {
        $planet = Planet::find($id);
        if($planet == null) {
            return response()->json(['error' => 'Seems this planet does not exist'], 404);
        }
        $resources = Resource::all();

        //price equation: ((C-(P+S))/(C+1)*k+1)*B = Pf
        // Pf final price, C = consume
        // S stock
        //k coefficient -> 0.5 by now
        // B base price
        //if the final price is below zero (great production and great offer), we take the base price
        //gonna need to implement the production (P) and move all this calculation in a service
        $market = [];
        foreach ($resources as $item) {
            $k = 0.5;
            $C = ceil($planet->population/100000*$item->rate_per_100k_population);
            $S = 1000; //use the market model to know the stock
            $P = 10000000;
            $B = $item->base_price;
            $Pf = ($C-($P+$S))/($C+1)*$k+1;
            $Pf = $Pf*$B;
            if($Pf <= 0) {
                $Pf = $B;
            }
            $market[] = ["resource" => $item->name, "buy" => ceil($Pf), "sell" => ceil($Pf - 0.01*$Pf) ];
        }
        $planet->market = $market;
        return $planet;
    }
}