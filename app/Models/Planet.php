<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Planet extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = [];
    protected $table = 'planets';
    public $timestamps = false;
    
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
