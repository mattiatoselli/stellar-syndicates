<?php

namespace App\Http\Controllers\V1\Ships;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship};

class ShipListController extends Controller
{
    /**
     * Ships Available.
     * Returns the list of the available ships for purchase.
     * @responseFile storage/responses/ships.json
     */
    public function __invoke()
    {
        return Ship::all();
    }
}