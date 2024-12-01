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

    public function __invoke(string $id)
    {
        $star = Star::with('planets')->find($id);

        if (!$star) { return response()->json([], 404); }

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

        return array(
            "star" => $star,
            "other_star" =>  $otherStars->sortBy('distance')->values(),
        );
    }
}
