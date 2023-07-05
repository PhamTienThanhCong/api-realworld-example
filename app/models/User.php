<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // fillable
    protected $fillable = [
        'username',
        'email',
        'password',
        'bio',
        'image'
    ];
    // hidden
    protected $hidden = [
        'password',
        'id',
        'created_at',
        'updated_at',
    ];
}
