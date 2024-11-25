<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//authentication
Route::post('/v1/authentication/register', \App\Http\Controllers\V1\Authentication\RegisterController::class);

//stars
Route::get('/v1/stars/list', \App\Http\Controllers\V1\Stars\StarsListController::class);
Route::get('/v1/stars/{id}', \App\Http\Controllers\V1\Stars\StarDetailsController::class);


Route::get('/v1/planets/list', \App\Http\Controllers\V1\Planets\PlanetsListController::class);

//resources
Route::get('/v1/resources/list', \App\Http\Controllers\V1\Resources\ResourceListController::class);

//ships
Route::get('/v1/ships/list', \App\Http\Controllers\V1\Ships\ShipListController::class);


//admin
Route::get('/v1/admin/generatedeposit', \App\Http\Controllers\V1\Admin\GenerateDepositsController::class);
