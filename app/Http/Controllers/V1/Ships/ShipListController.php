<?php

namespace App\Http\Controllers\V1\Ships;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ship};

class ShipListController extends Controller
{
    public function __invoke()
    {
        return Ship::all();
    }
}