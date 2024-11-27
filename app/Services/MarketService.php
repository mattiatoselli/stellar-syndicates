<?php

namespace App\Services;
use App\Models\{Planet, Resource, Market};

class MarketService
{
    const ELASTICITY = 0.5;

    /**
     * price equation: ((C-(P+S))/(C+1)*k+1)*B = Pf
     * Pf final price, C = consume
     * S stock
     * k coefficient -> 0.5 by now, it indicates the elasticity of the market to stress
     * B base price
     * if the final price is below zero (great production and great offer), we take the base price
     * 
     * the sell price is always a little bit less then the buy price in the same market
     */
    public function getMarketPrice(Planet $planet, Resource $item) : array
    {
        $market = Market::where('resource_id', $item->id)
                        ->where('planet_id', $planet->id)
                        ->first();
        $k = self::ELASTICITY;
        $C = ceil($planet->population/100000*$item->rate_per_100k_population);
        $S = $market->stock;
        $P = $market->base_production;
        $B = $item->base_price;
        $Pf = ($C-($P+$S))/($C+1)*$k+1;
        $Pf = $Pf*$B;
        if($Pf <= 0) { $Pf = $B; }
        return ["resource_id" => $item->id, "resource" => $item->name, "stock" => $market->stock, "production" => $market->base_production, "consume" => $C, "buy" => ceil($Pf), "sell" => ceil($Pf - 0.01*$Pf) ];
    }
}
