<?php

namespace App\Http\Controllers\V1\Ships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\ShipService;

class RefuelShipController extends Controller
{
    protected $ShipService;

    public function __construct(ShipService $ShipService)
    {
        $this->ShipService = $ShipService;
    }
    /**
     * Handle the ship purchase request.
     */
    public function __invoke(Request $request)
    {
        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }
        $this->ShipService->synchronize();
        $validated = $request->validate([
            'ship_id' => 'required|exists:users_ships,id',
            'amount' => 'required|integer|min:1'
        ]);
        $user_ship = UserShip::where('id', $validated['ship_id'])->where('user_id', $user->id)->first();
        if($user_ship == null) {
            return response()->json(['error' => 'No ship found'], 404);
        }
        $ship_model = Ship::find($user_ship->ship_id);
        if($validated['amount'] > ($ship_model->fuel_capacity - $user_ship->fuel)) {
            return response()->json(['error' => 'Fuel capacity exceeded'], 422);
        }

        // fuel price is one credit....
        if($user->credits < $validated['amount']) {
            return response()->json(['error' => 'Not enough credits'], 422);
        }
        if($user_ship->status != 'landed') {
            return response()->json(['error' => 'Only landed ships can refuel'], 422);
        }
        $user->credits -= $validated['amount'];
        $user_ship->fuel = $user_ship->fuel + $validated['amount'];
        $user->save();
        $user_ship->save();
        return $user_ship;
    }
}
