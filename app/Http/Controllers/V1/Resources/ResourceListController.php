<?php

namespace App\Http\Controllers\V1\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Resource, Planet, Deposit};

class ResourceListController extends Controller
{
    /**
     * Resources and goods list.
     * returns the list of base resources and of the goods available in the game
     */
    public function __invoke()
    {
        $resources = Resource::with(['firstBaseResource', 'secondBaseResource'])->get();
        $response = $resources->map(function ($resource) {
            return [
                "id" => $resource->id,
                "name" => $resource->name,
                "raw_material" => $resource->first_base_resource_id === null && $resource->second_base_resource_id === null ? 'true' : 'false',
                "first_resource" => $resource->first_base_resource_id === null ? null : $resource->firstBaseResource->name,
                "second_resource" => $resource->second_base_resource_id === null ? null : $resource->secondBaseResource->name,
                "first_base_resource_quantity" => $resource->first_base_resource_quantity,
                "second_base_resource_quantity" => $resource->second_base_resource_quantity,
                "base_price" => $resource->base_price,
                "added_value" => $resource->first_base_resource_id === null? 0 : $resource->base_price - $resource->firstBaseResource->base_price - $resource->secondBaseResource->base_price,
            ];
        });
    
        return response()->json($response);
    }
}