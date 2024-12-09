<?php

namespace App\Services;
use App\Models\{UserShip};

class ShipService
{
    /**
     * keeps UserShips in sync with their status
     */
    public function synchronize() : void
    {
        UserShip::whereIn('status', ['loading', 'unloading',])
            ->where('end_of_operation_time', '<=', now())
            ->update(['status' => 'landed', "end_of_operation_time" => null]);
        UserShip::whereIn('status', ['traveling', 'delivering', 'mining',])
            ->where('end_of_operation_time', '<=', now())
            ->update(['status' => 'stand-by', "end_of_operation_time" => null]);
    }
}
