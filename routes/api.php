<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Sanctum;
//authentication
Route::post('/v1/authentication/register', \App\Http\Controllers\V1\Authentication\RegisterController::class);
Route::get('/v1/authentication/user', function () { return response()->json(auth('sanctum')->user());});

//stars
Route::get('/v1/stars/list', \App\Http\Controllers\V1\Stars\StarsListController::class);
Route::get('/v1/stars/{id}', \App\Http\Controllers\V1\Stars\StarDetailsController::class);

//planets 
Route::get('/v1/planets/list', \App\Http\Controllers\V1\Planets\PlanetsListController::class);
Route::get('/v1/planets/{id}', \App\Http\Controllers\V1\Planets\PlanetDetailsController::class);

//resources
Route::get('/v1/resources/list', \App\Http\Controllers\V1\Resources\ResourceListController::class);

//ships
Route::get('/v1/ships/list', \App\Http\Controllers\V1\Ships\ShipListController::class);
Route::post('/v1/ships/buy', \App\Http\Controllers\V1\Ships\BuyShipController::class);
Route::get('/v1/ships/status', \App\Http\Controllers\V1\Ships\ShipStatusController::class);

//admin
Route::get('/v1/admin/generatedeposit', \App\Http\Controllers\V1\Admin\GenerateDepositsController::class);
