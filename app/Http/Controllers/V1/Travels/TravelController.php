<?php

namespace App\Http\Controllers\V1\Travels;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, CargoItem, Resource};
use Illuminate\Support\Facades\Auth;


/**
 * Provide the id of the usership and the id of the planet to begin a travel.
 * travel consumes water (must be stored in the cargo of the ship) immediately
 * ship's status must be in stand-by or landed in order to begin a travel.
 * Travel speed and consumption is affected by speed and efficiency of the ship
 */
class TravelController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }

        $validated = $request->validate([
            'user_ship_id' => 'required|exists:users_ships,id',
            'planet_id' => 'required|exists:planets,id',
        ]);

        UserShip::where('user_id', $user->id)
            ->whereNotIn('status', ['stand-by', 'landed'])
            ->where('end_of_operation_time', '<=', now())
            ->update(['status' => 'stand-by', "end_of_operation_time" => null]);
        
        $ship = UserShip::where('id', $validated['user_ship_id'])
                    ->where('user_id', $user->id)
                    ->first();
                
        

        //ship must be landed or in stan-by to begin a travel, and of course must belong to the user
        if($ship == null) {
            return response()->json(['error' => 'The ship provided does not belong to the logged user'], 401);
        } else if($ship->end_of_operation_time != null) {
            return response()->json(['error' => 'This ship is performing an operation'], 422);
        }

        //check if the usership has enough fuel (water)
        $goal_planet = Planet::find($validated['planet_id'])
        $starting_system = Star::find($ship->star_location_id);
        $goal_system = Star::find($goal_planet->star_id);
        $ship_model = Ship::find();
        $fuel_resource = Resource::where('name', 'Water')->first();
        $fuel = CargoItem::where('user_ship_id', $ship->id)
                            ->where('resource_id', $fuel_resource->id)
                            ->first();

        if($fuel == null) {
            return response()->json(['error' => 'No water stored in cargo, ship does not have enough fuel'], 422);
        }

        $distance = sqrt(
            pow($goal_system->x - $starting_system->x, 2) +
            pow($goal_system->y - $starting_system->y, 2) +
            pow($goal_system->z - $starting_system->z, 2)
        );
        $travel_time = ceil(($distance / $ship_model->speed));
        $fuel_burned = ceil($ship_model->fuel_distance_ratio*$distance);

        if($fuel->quantity <= $fuel_burned) {
            return response()->json(['error' => 'Not enough water in cargo for the travel.'], 422);
        }

        //ship will be moved to the new planet.
        //there will be one minute more of travel time to simulate the moving beetween planets
        //this is also because we want to avoid instantaneous travels beetween planets of the same system

        return [];
    }
}