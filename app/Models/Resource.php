<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $keyType = 'string';
    protected $hidden = [];
    protected $table = 'resources';
}
