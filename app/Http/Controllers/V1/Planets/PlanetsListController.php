<?php

namespace App\Http\Controllers\V1\Planets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Planet};

class PlanetsListController extends Controller
{
    public function __invoke()
    {
        return Planet::all();
    }
}