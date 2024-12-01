<?php

namespace App\Http\Controllers\V1\Planets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Planet, Deposit};

class PlanetsListController extends Controller
{
    public function __invoke()
    {
        return Planet::orderBy('population', 'desc')->paginate(100);
    }
}