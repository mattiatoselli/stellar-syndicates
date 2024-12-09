<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(database_path('data\resources.json'));
        $resources = json_decode($json, true);
        Resource::truncate();
        foreach($resources as $item) {
            Resource::insert([
                "id" =>  $item['id'],
                "name" =>  $item['name'],
                "base_price" =>  $item['base_price'],
                "description" =>  $item['description'],
                "prospection_chance" =>  $item['prospection_chance'],
                "first_base_resource_id" =>  $item['first_base_resource_id'],
                "second_base_resource_id" =>  $item['second_base_resource_id'],
                "first_base_resource_quantity" =>  $item['first_base_resource_quantity'],
                "second_base_resource_quantity" =>  $item['second_base_resource_quantity'],
                "rate_per_100k_population" => $item["rate_per_100k_population"],
            ]);
        }
    }
}
