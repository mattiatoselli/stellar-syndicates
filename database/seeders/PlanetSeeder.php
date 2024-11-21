<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\{Star, Planet};

class PlanetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $planetTypes = [
            'Gas Giant', 
            'Rocky Planet', 
            'Ice Planet', 
            'Terrestrial Planet', 
            'Desert Planet', 
            'Ocean Planet',
            'Zero Gravity Planet',
            'Radioactive Planet'
        ];

        // Carichiamo i dati delle stelle
        $stars = Star::all();

        // Carichiamo i nomi dei pianeti dal file JSON
        $planetNames = json_decode(File::get(database_path('data/planets.json')), true);

        // Cicliamo su tutte le stelle per assegnare i pianeti
        foreach ($stars as $star) {
            // Numero casuale di pianeti tra 0 e 7
            $numPlanets = rand(0, 7);
            $usedDistances = []; // Array per tenere traccia delle distanze già utilizzate

            // Cicliamo per creare i pianeti
            for ($i = 0; $i < $numPlanets; $i++) {
                $planetType = $planetTypes[array_rand($planetTypes)];

                $planetName = array_shift($planetNames); 

                do {
                    $distanceFromStar = rand(1, 500);
                } while (in_array($distanceFromStar, $usedDistances)); // not same distance from star for a planet in same star

                $usedDistances[] = $distanceFromStar;

                $population = 0; // Non terrestrial planets are not suitable for human life, we still did not achieved terraformation tech
                if ($planetType == 'Terrestrial Planet') {
                    $population = rand(10000, 20000000000); // population beetween 10k and 20 billions
                }
                $planet = new Planet();
                $planet->star_id = $star->id;
                $planet->name = $planetName;
                $planet->distance_from_star = $distanceFromStar;
                $planet->type = $planetType;
                $planet->population = $population; 
                $planet->save();
            }
        }
    }
}
