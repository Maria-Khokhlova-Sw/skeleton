<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'login',
        'password',
        'id_role',
        'is_blocked',
        'number_attempt',
    ];
}

