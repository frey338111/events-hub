<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_reset';

    protected $fillable = [
        'token',
        'customer_email',
        'status',
    ];
}
