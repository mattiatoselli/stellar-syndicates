<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    protected $hidden = [];
    protected $table = 'markets';
    public $timestamps = false;
}
