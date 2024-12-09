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


    /**
     * Planet's details.
     * Returns the planet's details, with his market, and deposits.
     * @urlParam $id required. Planets's Id. Example: a6863bac-d92f-4500-942f-9c04e4528a2e
     * @responseFile storage/responses/planet_details.json
     */
    public function __invoke(string $id)
    {
        $planet = Planet::find($id);
        if($planet == null) {
            return response()->json(['error' => 'Seems this planet does not exist'], 404);
        }
        $resources = Resource::all();
        $market = [];
        foreach ($resources as $item) {
            $market[] = $this->MarketService->getMarketPrice($planet, $item);
        }
        $planet->market = $market;
        $planet->deposits = Deposit::where('planet_id', $planet->id)->where('status', '!=', 'hidden')->get();

        return $planet;
    }
}