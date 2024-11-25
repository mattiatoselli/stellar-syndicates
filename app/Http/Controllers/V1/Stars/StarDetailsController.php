<?php

namespace App\Http\Controllers\V1\Stars;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Star, Planet, Deposit};
use Illuminate\Support\Facades\DB;

class StarDetailsController extends Controller
{
    public function __invoke(string $id)
    {
        $star = Star::find($id);
        if($star == null) {
            return response()->status(404);
        }
        $otherStars = Star::where('id', '!=', $id)->get();
        $planets = Planet::where('star_id', $star->id)->get();
        foreach ($planets as &$planet) {
            $planet->deposits = Deposit::where("planet_id", $planet->id)->where("status", "discovered")->get();
        }

        $star->planets = $planets;

        $distances = $otherStars->map(function ($otherStar) use ($star) {
            $distance = sqrt(
                pow($otherStar->x - $star->x, 2) +
                pow($otherStar->y - $star->y, 2) +
                pow($otherStar->z - $star->z, 2)
            );
    
            return [
                'id' => $otherStar->id,
                'name' => $otherStar->name,
                'distance' => round($distance, 2),
            ];
        });

        $star->distances = $distances->sortBy('distance')->values();;
        return $star;
    }
}