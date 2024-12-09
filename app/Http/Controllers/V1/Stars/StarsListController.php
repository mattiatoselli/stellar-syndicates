<?php

namespace App\Http\Controllers\V1\Stars;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Star, Planet};

class StarsListController extends Controller
{
    /**
     * List Stars.
     * Returns the list of the stars in the universe.
     * List is paginated.
     * @responseFile storage/responses/stars.json
     */
    public function __invoke()
    {
        return Star::paginate(100);
    }
}