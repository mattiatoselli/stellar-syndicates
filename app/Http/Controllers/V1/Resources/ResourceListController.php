<?php

namespace App\Http\Controllers\V1\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Resource};

class ResourceListController extends Controller
{
    public function __invoke()
    {
        return Resource::all();
    }
}