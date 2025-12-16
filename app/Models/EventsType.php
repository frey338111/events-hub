<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsType extends Model
{
    protected $table = 'events_type';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function events()
    {
        return $this->hasMany(Events::class, 'type_id');
    }
}
