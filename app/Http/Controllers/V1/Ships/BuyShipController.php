<?php

namespace App\Http\Controllers\V1\Ships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BuyShipController extends Controller
{
    /**
     * Handle the ship purchase request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'ship_id' => 'required|exists:ships,id',
            'star_id' => 'required|exists:stars,id',
            'planet_id' => 'required|exists:planets,id',
        ]);

        $shipId = $validated['ship_id'];
        $starId = $validated['star_id'];
        $planetId = $validated['planet_id'];

        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }
        $capitalStarId = '933f78f1-fed4-4da5-992b-556ec6ae11bc'; // Federation capital star ID

        // Validate the selected planet belongs to the specified star
        $planet = Planet::where('id', $planetId)->where('star_id', $starId)->first();
        if (!$planet) {
            return response()->json(['error' => 'The specified planet does not belong to the given star.'], 422);
        }
        $destinationStar = Star::find($starId);

        // Validate the planet is a Terrestrial Planet
        if ($planet->type !== 'Terrestrial Planet') {
            return response()->json(['error' => 'Ships can only be delivered to Terrestrial Planets.'], 403);
        }

        // Validate the user's balance
        $ship = Ship::findOrFail($shipId);
        /*if ($user->credits < $ship->price) {
            return response()->json(['error' => 'Insufficient funds to purchase this ship.'], 403);
        }*/

        // Calculate delivery time based on distance from the capital star and the destination star
        $capitalStar = Star::findOrFail($capitalStarId);
        $deliveryDistance = sqrt(
            pow($destinationStar->x - $capitalStar->x, 2) +
            pow($destinationStar->y - $capitalStar->y, 2) +
            pow($destinationStar->z - $capitalStar->z, 2)
        );
        $deliveryTime = ceil(($deliveryDistance / $ship->speed));

        // Deduct the ship price from user's balance
        $user->credits -= $ship->price;
        $user->save();

        // Create the user's ship record
        $userShip = new UserShip();
        $userShip->user_id = $user->id;
        $userShip->ship_id = $ship->id;
        $userShip->star_location_id = $starId;
        $userShip->planet_location_id = $planetId;
        $userShip->status = 'delivering';
        $userShip->end_of_operation_time = now()->addMinutes($deliveryTime);
        $userShip->save();
        return $userShip;
    }
}
