<?php

namespace App\Http\Controllers\V1\Planets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Planet, Deposit};

class PlanetsListController extends Controller
{
    public function __invoke()
    {
        $planets = Planet::all();
        foreach ($planets as &$planet) {
            $planet->deposits = Deposit::where('planet_id', $planet->id)->where('status', '!=', 'hidden')->get();
        }

        return $planets;
    }
}