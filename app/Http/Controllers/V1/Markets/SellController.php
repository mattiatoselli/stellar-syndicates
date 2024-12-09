<?php

namespace App\Http\Controllers\V1\Markets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthenticationRegisterRequest;
use App\Models\{User, Market, Planet, Resource, UserShip, Ship, CargoItem};
use App\Services\{MarketService, ShipService};
use Illuminate\Support\Facades\Schema;

class SellController extends Controller
{
    protected $MarketService;
    protected $ShipService;

    public function __construct(MarketService $MarketService, ShipService $ShipService)
    {
        $this->MarketService = $MarketService;
        $this->ShipService = $ShipService;
    }

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'user_ship_id' => 'required|exists:users_ships,id',
            'resource_id' => 'required|exists:resources,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }

        $this->ShipService->synchronize();

        $user_ship = UserShip::where("user_id", $user->id)->where('id', $validated['user_ship_id'])->first();
        
        if($user_ship == null) {
            return response()->json(['error' => 'No ship found, please provide the user_ship_id'], 404);
        }
        
        $planet = Planet::find($user_ship->planet_location_id);
        $market = Market::where('planet_id', $user_ship->planet_location_id)
                    ->where('resource_id', $validated['resource_id'])->first();
        $resource = Resource::find($validated['resource_id']);
        $ship_model = Ship::find($user_ship->ship_id);

        
        
        //only landed ships can trade
        if($user_ship->status != 'landed') {
            return response()->json(['error' => 'Only landed ships can trade.'], 422);
        }

        //if market is null, user is trying to trade on a non terrestrial planet (might change in future)
        if($market == null) {
            return response()->json(['error' => 'No market available here, are you in a populated planet?'], 422);
        }


        $price = $this->MarketService->getMarketPrice($planet, $resource);

        //now that we checked everything, we can begin the cargo operation
        $cargo_item = CargoItem::where('user_ship_id', $user_ship->id)->where('resource_id', $resource->id)->first();
        if ($cargo_item == null) {
            return response()->json(["message" => "No resources with that id in cargo."], 422);
        }
        $cargo_item->quantity = $cargo_item->quantity - $validated['quantity'];
        $user->credits += $price['sell']*$validated['quantity'];
        $user_ship->status = 'unloading';
        $load_seconds = $validated['quantity']/$ship_model->cargo_speed*60;
        $user_ship->end_of_operation_time = now()->addSeconds($load_seconds);
        $market->stock = $market->stock + $validated['quantity'];

        if($cargo_item->quantity < 0) {
            return response()->json(["message" => "Trying to sell more quantity than the amount stored in cargo"], 422);
        }
        //flash into the db
        $user_ship->save();
        $user->save();
        $cargo_item->save();
        $market->save();

        if($cargo_item->quantity == 0) {
            $cargo_item->delete();
        }

        return [
            "user" => $user,
            "ship" => array_merge($user_ship->toArray(), $ship_model->toArray()),
            "cargo" => $user_ship->cargo_items,
        ];
    }
}