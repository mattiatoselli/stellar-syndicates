<?php

namespace App\Http\Controllers\V1\Markets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthenticationRegisterRequest;
use App\Models\{User, Market, Planet, Resource, UserShip, CargoItem};
use App\Services\MarketService;

class BuyController extends Controller
{
    protected $MarketService;

    public function __construct(MarketService $MarketService)
    {
        $this->MarketService = $MarketService;
    }

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'user_ship_id' => 'required|exists:users_ships,id',
            'resource_id' => 'required|exists:resources,name',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }

        $user_ship = UserShip::where("user_id", $user->id)
                        ->where('id', $validated['user_ship_id'])
                        ->first();
        $planet = Planet::find($user_ship->planet_location_id);
        $market = Market::where('planet_id', $user_ship->planet_location_id)
                    ->where('resource_id', $validated['resource_id'])->first();
        $resource = Resource::find($validated['resource_id']);
        $ship_model = Ship::find($user_ship->ship_id);

        if($user_ship == null) {
            return response()->json(['error' => 'No ship found, please provide the user_ship_id'], 404);
        }
        
        //only landed ships can trade
        if($user_ship->status != 'landed') {
            return response()->json(['error' => 'Only landed ships can trade.'], 422);
        }

        //if market is null, user is trying to trade on a non terrestrial planet (might change in future)
        if($market == null) {
            return response()->json(['error' => 'No market available here, are you in a populated planet?'], 422);
        }

        //check if planet has enough stock
        if($validated['quantity'] > $market->stock) {
            return response()->json(['error' => 'Not enough resources stocked in the planenet\'s market.'], 422);
        }

        //check if the user has enough credits
        $price = $this->MarketService->getMarketPrice($planet, $resource);
        if($price['buy']*$validated['quantity'] > $user->credits) {
            return response()->json(['error' => 'Not enough credits.'], 422);
        }

        //check if ship has enough cargo space
        $cargo_left_space = $ship->cargo - CargoItem::where('user_ship_id', $user_ship->id)->sum();
        if($cargo_left_space <= 0) {
            return response()->json(['error' => 'Not enough space left in cargo.'], 422);
        }

        //now that we checked everything, we can begin the cargo operation
        $cargo_item = CargoItem::where('user_ship_id', $user_ship->id)
                        ->where('resource_id', $resource->id)->first();
        if($cargo_item == null) {
            $cargo_item = new CargoItem();
            $cargo_item->user_ship_id = $user_ship->id;
            $cargo_item->resource_id = $resource->id;
            $cargo_item->quantity = 0;
        }
        $cargo_item->quantity += $validated['quantity'];

        $user->credits -= $price['buy']*$validated['quantity'];

        $user_ship->status = 'loading';
        $load_seconds = $validated['quantity']/$ship->cargo_speed*10;
        $user_ship->end_of_operation_time = now()->addSeconds($load_seconds);

        $market->stock =- $validated['quantity'];

        //flash into the db
        $user_ship->save();
        $user->save();
        $cargo_item->save();
        $market->save();
    }
}