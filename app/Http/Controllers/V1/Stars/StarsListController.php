<?php

namespace App\Http\Controllers\V1\Stars;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Star, Planet};

class StarsListController extends Controller
{
    public function __invoke()
    {
        return Star::with('planets')->get();

    }
}