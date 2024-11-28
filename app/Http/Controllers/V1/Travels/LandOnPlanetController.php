<?php

namespace App\Http\Controllers\V1\Travels;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, CargoItem, Resource};
use Illuminate\Support\Facades\Auth;


/**
 * 
 */
class LandOnPlanetController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }

        $validated = $request->validate(['user_ship_id' => 'required|exists:users_ships,id',]);
        UserShip::where('user_id', $user->id)
            ->whereIn('status', ['loading'])
            ->where('end_of_operation_time', '<=', now())
            ->update(['status' => 'landed', "end_of_operation_time" => null]);
        UserShip::where('user_id', $user->id)
            ->whereIn('status', ['traveling', 'delivering'])
            ->where('end_of_operation_time', '<=', now())
            ->update(['status' => 'stand-by', "end_of_operation_time" => null]);
        $ship = UserShip::where('id', $validated['user_ship_id'])
                    ->where('user_id', $user->id)
                    ->first();

        $planet = Planet::where('id', $ship->planet_location_id)->first();
        if($planet->type != "Terrestrial Planet") {
            return response()->json(['You can land only on terrestrial planets'], 422);
        }
        $ship->status = 'landed';
        $ship->end_of_operation_time = null;
        $ship->save();
        return $ship;
    }
}