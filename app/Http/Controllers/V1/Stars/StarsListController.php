<?php

namespace App\Http\Controllers\V1\Stars;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Star;

class StarsListController extends Controller
{
    public function __invoke()
    {
        return Star::all();
    }
}