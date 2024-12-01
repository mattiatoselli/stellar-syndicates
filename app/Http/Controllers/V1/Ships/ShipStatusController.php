<?php

namespace App\Http\Controllers\V1\Ships;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship, UserShip, Planet, Star, CargoItem};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\ShipService;

class ShipStatusController extends Controller
{
    protected $ShipService;

    public function __construct(ShipService $ShipService)
    {
        $this->ShipService = $ShipService; 
    }
    public function __invoke()
    {
        $user = auth('sanctum')->user();
        if($user == null) {
            return response()->json(['error' => 'Provide authentication token in the authorization header, format should be "Bearer <auth_token>"'], 401);
        }
        $this->ShipService->synchronize();
        return UserShip::with('cargo_items')->where('user_id', $user->id)->get();
    }
}
