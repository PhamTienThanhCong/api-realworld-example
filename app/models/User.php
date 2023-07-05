<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

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