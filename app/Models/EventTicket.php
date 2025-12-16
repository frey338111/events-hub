<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    protected $table = 'event_ticket';

    protected $fillable = [
        'event_id',
        'customer_id',
        'status',
        'hash_key',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
