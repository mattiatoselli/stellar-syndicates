<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = [];
    protected $table = 'deposits';

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
