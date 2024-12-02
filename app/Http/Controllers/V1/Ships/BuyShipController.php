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
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'ship_id' => 'required|exists:ships,id',
        ]);

        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }
        $planet = Planet::where('name', "Earth")->first();
        $ship = Ship::findOrFail($validated['ship_id']);

        // Deduct the ship price from user's balance
        if($user->credits < $ship->price) {
            return response()->json(['error' => 'Not enough credits'], 422);
        }
        $user->credits -= $ship->price;
        $user->save();

        // Create the user's ship record
        $userShip = new UserShip();
        $userShip->user_id = $user->id;
        $userShip->ship_id = $ship->id;
        $userShip->star_location_id = $planet->star_id;
        $userShip->planet_location_id = $planet->id;
        $userShip->status = 'stand-by';
        $userShip->end_of_operation_time = null;
        $userShip->fuel = 0;
        $userShip->save();
        return $userShip;
    }
}
