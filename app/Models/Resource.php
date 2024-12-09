<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $keyType = 'string';
    protected $hidden = [];
    protected $table = 'resources';
    public $timestamps = false;

    public function firstBaseResource()
    {
        return $this->belongsTo(Resource::class, 'first_base_resource_id');
    }

    public function secondBaseResource()
    {
        return $this->belongsTo(Resource::class, 'second_base_resource_id');
    }
}
