<?php

namespace App\Http\Controllers\V1\Ships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, CargoItem};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShipStatusController extends Controller
{
    /**
     * Handle the ship purchase request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }
        //while we are here let us check if the status of the ships of this user need an update.
        //then we return the ships
        UserShip::where('user_id', $user->id)
            ->whereNotIn('status', ['stand-by', 'landed'])
            ->where('end_of_operation_time', '<=', now())
            ->update(['status' => 'stand-by', "end_of_operation_time" => null]);

        return UserShip::with('cargo_items')->where('user_id', $user->id)->get();
    }
}
