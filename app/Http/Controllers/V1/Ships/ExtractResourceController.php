<?php

namespace App\Http\Controllers\V1\Ships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, Resource, Deposit, CargoItem};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\ShipService;

class ExtractResourceController extends Controller
{
    protected $ShipService;

    public function __construct(ShipService $ShipService)
    {
        $this->ShipService = $ShipService;
    }
    /**
     * extract resources
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
            'amount' => 'required|integer|min:1',
            'deposit_id' => 'required|exists:deposits,id',
        ]);
        
        //check user ship
        $user_ship = UserShip::where('id', $validated['ship_id'])->where('user_id', $user->id)->first();
        if($user_ship == null) {
            return response()->json(['error' => 'No ship found'], 404);
        }
        if($user_ship->status != 'stand-by') {
            return response()->json(['error' => 'Only ships in stand-by can mine a deposit.'], 404);
        }
        $ship_model = Ship::find($user_ship->ship_id);

        //check if deposit exists
        $deposit = Deposit::where('id', $validated['deposit_id'])
                    ->where('planet_id', $user_ship->planet_location_id)
                    ->first();
        
        if($deposit == null) {
            return response()->json(['error' => 'Deposit not found. is the ship in stand-by on the right planet?'], 404);
        }

        //check space in cargo
        $cargo_left_space = $ship_model->cargo - CargoItem::where('user_ship_id', $user_ship->id)->sum('quantity');
        if($cargo_left_space < $validated['amount']) {
            return response()->json(['error' => 'Not enough space left in cargo.'], 422);
        }

        //check if the deposit contains enough material
        if($validated['amount'] > $deposit->quantity) {
            return response()->json(['error' => 'Extraction amount cannot be higher than the quantity in the deposit.'], 422);
        }


        //extract resource
        $cargo_item = CargoItem::where('user_ship_id', $user_ship->id)
                        ->where('resource_id', $deposit->resource_id)->first();
        if($cargo_item == null) {
            $cargo_item = new CargoItem();
            $cargo_item->user_ship_id = $user_ship->id;
            $cargo_item->resource_id = $deposit->resource_id;
            $cargo_item->quantity = 0;
        }

        $resource = Resource::find($deposit->resource_id);
        
        //the mining speed is given by the mining capacity of the ship, and by the rarity of the resource
        $user_ship->status = 'mining';
        $load_seconds = $validated['amount']/$ship_model->mining_speed*60;
        $load_seconds = $load_seconds/($resource->prospection_chance/10);
        $user_ship->end_of_operation_time = now()->addSeconds($load_seconds);
        $cargo_item->quantity = $cargo_item->quantity + $validated['amount'];
        $deposit->quantity = $deposit->quantity - $validated['amount'];
        
        $user_ship->save();
        $cargo_item->save();
        $deposit->save();

        return UserShip::with('cargo_items')->where('id', $user_ship->id)->first();;
    }
}
