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

    public function isFollowing($user_id)
    {
        // check in followers table
        $follow = Follow::where('user_id', auth()->user()->id)
            ->where('following_user_id', $user_id)
            ->first();
        
        if ($follow) {
            return true;
        } else {
            return false;
        }
    }
}
