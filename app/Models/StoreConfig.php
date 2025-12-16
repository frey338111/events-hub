<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreConfig extends Model
{
    protected $table = 'store_config';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
    ];
}
