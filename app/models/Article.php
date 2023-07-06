<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'slug',
        'description',
        'body',
        'tagList',
        'author_id'
    ];

    protected $hidden = [
        'id',
        'user_id'
    ];

    public function getTagAttribute($value)
    {
        return explode(',', $value);
    }

    public function getAuthor($user_id)
    {
        $user = User::find($user_id);
        $user->following = $user->isFollowing($user_id);
        return $user;
    }
    
}
