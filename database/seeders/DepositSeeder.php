<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Resource, Planet, Deposit};

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $possible_status = ['hidden', 'discovered'];
        $planets = Planet::all();
        $resources = Resource::where("first_base_resource_id", null)->where("second_base_resource_id", null)->get();
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
    }
}
