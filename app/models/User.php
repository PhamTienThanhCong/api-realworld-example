<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
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

    public function isFollowing($my_user_id)
    {
        $user_id = $this->attributes['id'];
        $isFollowing = DB::table('follows')
            ->where('user_id', $my_user_id)
            ->where('following_user_id', $user_id)
            ->count();
        if ($isFollowing > 0) {
            $this->attributes['following'] = true;
        } else {
            $this->attributes['following'] = false;
        }
    }
}
