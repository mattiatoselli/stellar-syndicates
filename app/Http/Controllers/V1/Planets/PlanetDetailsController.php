<?php

namespace App\Http\Controllers\V1\Planets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Planet, Resource, Deposit};
use Illuminate\Support\Facades\File;
use App\Services\MarketService;

class PlanetDetailsController extends Controller
{
    protected $marketService;

    public function __construct(MarketService $MarketService)
    {
        $this->MarketService = $MarketService;
    }


    public function __invoke(string $id)
    {
        $planet = Planet::find($id);
        if($planet == null) {
            return response()->json(['error' => 'Seems this planet does not exist'], 404);
        }
        $resources = Resource::all();
        $market = [];
        if($planet->type != "Terrestrial Planet") {
            $planet->market = $market;
            $planet->deposits = Deposit::where('planet_id', $planet->id)->where('status', '!=', 'hidden')->get();
            return $planet;
        }
        foreach ($resources as $item) {
            $market[] = $this->MarketService->getMarketPrice($planet, $item);
        }
        $planet->market = $market;
        $planet->deposits = Deposit::where('planet_id', $planet->id)->where('status', '!=', 'hidden')->get();

        return $planet;
    }
}