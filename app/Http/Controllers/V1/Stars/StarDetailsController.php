<?php

namespace App\Http\Controllers\V1\Stars;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Star, Planet};
use App\Services\TravelService;

class StarDetailsController extends Controller
{
    protected $travelService;

    public function __construct(TravelService $travelService)
    {
        $this->travelService = $travelService;
    }

    /**
     * Star's details.
     * Returns the star's details with the planets orbiting around the star.
     * @urlParam $id required. Star's Id. Example: 71ff81a3-47e5-426e-892f-553517c18c8c
     * @responseFile storage/responses/star_details.json
     */
    public function __invoke(string $id)
    {
        $star = Star::with('planets')->find($id);

        if (!$star) { return response()->json(["message" => "Not found."], 404); }

        /*
        $otherStars = Star::where('id', '!=', $id)
            ->withCount(['planets as terrestrial_planets_count' => function ($query) {
                $query->where('type', 'Terrestrial Planet');
            }])
            ->get()
            ->map(function ($item) use ($star) {
                $item->distance = $this->travelService->calculateDistance($item, $star);
                return $item;
            });
        foreach ($otherStars as &$item) {
            $item->distance = $this->travelService->calculateDistance($item, $star);
        }
            */

        return $star;
        /*array(
            "star" => $star,
            //"other_star" =>  $otherStars->sortBy('distance')->values(),
        );*/
    }
}
