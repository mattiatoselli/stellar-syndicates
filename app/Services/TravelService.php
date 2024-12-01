<?php

namespace App\Services;
use App\Models\{Star, Ship};

class TravelService
{
    /**
     * returns the distance of a travel
     */
    public function calculateDistance(Star $destination, Star $starting) : int
    {
        $distance = sqrt(
            pow($destination->x - $starting->x, 2) +
            pow($destination->y - $starting->y, 2) +
            pow($destination->z - $starting->z, 2)
        );
        return ceil($distance);
    }

    /**
     * Returns travel time for a certain ship
     * we add a minute so that even travels beetween same systems' planets are not for free
     */
    public function calculateTravelTime(Ship $ship, int $distance) : int
    {
        $travelTimeInSeconds = floor(($distance / $ship->speed)) + 1;
        return $travelTimeInSeconds*60;
    }

    /**
     * returns the fuel burned
    */
    public function calculateNecessaryFuel(Ship $ship, int $distance) : int
    {
        return floor($distance/$ship->fuel_distance_ratio);
    }
}
