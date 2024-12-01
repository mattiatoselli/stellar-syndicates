<?php

namespace App\Http\Controllers\V1\Travels;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, CargoItem, Resource};
use Illuminate\Support\Facades\Auth;
use App\Services\{TravelService};

/**
 * Provides data about the amount of fuel and the time necessary for the travel
 * need to provide a user_ship_id and the destination planet id
 */
class TravelDetailsController extends Controller
{
    protected $TravelService;

    public function __construct(TravelService $TravelService)
    {
        $this->TravelService = $TravelService;
    }


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
        $ship = UserShip::where('id', $validated['user_ship_id'])
                    ->where('user_id', $user->id)
                    ->first();
                
        if($ship == null) {
            return response()->json(['error' => 'The ship provided does not belong to the logged user'], 401);
        }

        //check if the usership has enough fuel (water)
        $goal_planet = Planet::find($validated['planet_id']);
        $starting_system = Star::find($ship->star_location_id);
        $goal_system = Star::find($goal_planet->star_id);
        $ship_model = Ship::find($ship->ship_id);

        $distance =  $this->TravelService->calculateDistance($starting_system, $goal_system);
        $travel_time = $this->TravelService->calculateTravelTime($ship_model, $distance);
        $fuel_burned = $this->TravelService->calculateNecessaryFuel($ship_model, $distance);

        return [
            "distance" => $distance, 
            "travel_time_in_seconds" => $travel_time,
            "fuel" => $fuel_burned];
    }
}