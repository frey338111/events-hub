<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsLocation extends Model
{
    protected $table = 'events_location';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function events()
    {
        return $this->hasMany(Events::class, 'location_id');
    }
}
