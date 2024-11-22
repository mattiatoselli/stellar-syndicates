<?php

namespace App\Http\Controllers\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Resource, Planet, Deposit};

class GenerateDepositsController extends Controller
{
    public function __invoke()
    {
        //metals
        $possible_status = ['hidden', 'discovered'];
        $planets = Planet::whereIn('type', ['Rocky Planet', 'Gas Giant', 'Desert Planet'])->get();
        $resources = [1,2,3,4,5,6,7,9,10,13,14,15,16,17,18,19,20,22];
        $resources = Resource::find($resources);
        for ($i=0; $i<1000; $i++) {
            $random_planet = $planets->random();
            $random_resource = $resources->random();
            $random_status = $possible_status[array_rand($possible_status)];
            $deposit = new Deposit();
            $deposit->planet_id = $random_planet->id;
            $deposit->resource_id = $random_resource->id;
            $deposit->status = $random_status;
            $deposit->quantity = round(rand(1000000,10000000)*$random_resource->prospection_chance/100);
            $deposit->planet_id = $random_planet->id;
            $deposit->save();
        }

        //radioactive resources
        $possible_status = ['hidden', 'discovered'];
        $planets = Planet::whereIn('type', ['Radioactive Planet'])->get();
        $resources = [8,21];
        $resources = Resource::find($resources);
        for ($i=0; $i<1000; $i++) {
            $random_planet = $planets->random();
            $random_resource = $resources->random();
            $random_status = $possible_status[array_rand($possible_status)];
            $deposit = new Deposit();
            $deposit->planet_id = $random_planet->id;
            $deposit->resource_id = $random_resource->id;
            $deposit->status = $random_status;
            $deposit->quantity = round(rand(1000000,10000000)*$random_resource->prospection_chance/100);
            $deposit->planet_id = $random_planet->id;
            $deposit->save();
        }

        $possible_status = ['hidden', 'discovered'];
        $planets = Planet::whereIn('type', ['Ice Planet', 'Ocean Planet',])->get();
        $resources = [12,23,24,25,26,27];
        $resources = Resource::find($resources);
        for ($i=0; $i<1000; $i++) {
            $random_planet = $planets->random();
            $random_resource = $resources->random();
            $random_status = $possible_status[array_rand($possible_status)];
            $deposit = new Deposit();
            $deposit->planet_id = $random_planet->id;
            $deposit->resource_id = $random_resource->id;
            $deposit->status = $random_status;
            $deposit->quantity = round(rand(1000000,10000000)*$random_resource->prospection_chance/100);
            $deposit->planet_id = $random_planet->id;
            $deposit->save();
        }
        return Deposit::all();
    }
}