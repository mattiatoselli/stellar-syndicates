<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StarSeeder::class,
            PlanetSeeder::class,
            ResourceSeeder::class,
            MarketSeeder::class,
            DepositSeeder::class,
            ShipSeeder::class,
        ]);
    }
}
