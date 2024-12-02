<?php

namespace App\Http\Controllers\V1\Travels;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, CargoItem, Resource};
use Illuminate\Support\Facades\Auth;
use App\Services\ShipService;

/**
 * 
 */
class LandOnPlanetController extends Controller
{
    public function __construct(ShipService $ShipService)
    {
        $this->ShipService = $ShipService;
    }

    public function __invoke(Request $request)
    {
        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }

        $validated = $request->validate(['user_ship_id' => 'required|exists:users_ships,id',]);
        
        $this->ShipService->synchronize();

        $ship = UserShip::where('id', $validated['user_ship_id'])
                    ->where('user_id', $user->id)
                    ->first();
        if($ship->status != 'stand-by') {
            return response()->json(['error' => 'Only ships in stand-by can land on a planet'], 422);
        }
        $ship->status = 'landed';
        $ship->end_of_operation_time = null;
        $ship->save();
        return $ship;
    }
}