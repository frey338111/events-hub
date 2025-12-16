<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'url_key',
        'type_id',
        'description',
        'start_time',
        'end_time',
        'location_id',
        'events_image',
        'events_thumbnail',
        'capacity',
        'customer_id',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected $appends = ['booked_count', 'customer_name'];

    public function type()
    {
        return $this->belongsTo(EventsType::class, 'type_id');
    }

    public function location()
    {
        return $this->belongsTo(EventsLocation::class, 'location_id');
    }

    public function tickets()
    {
        return $this->hasMany(\App\Models\EventTicket::class, 'event_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getBookedCountAttribute()
    {
        return EventTicket::where('event_id', $this->id)
            ->where('status', 'hold')
            ->count();
    }

    public function getCustomerNameAttribute(): string
    {
        if (empty($this->customer_id)) {
            return 'System';
        }

        return $this->customer?->name ?? 'System';
    }
}
