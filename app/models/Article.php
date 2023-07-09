<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    public function getTagAttribute()
    {
        $value = $this->attributes['tagList'];
        return explode(',', $value);
    }

    public function getAuthor($my_user_id)
    {
        $user = User::find($this->attributes['user_id']);
        $user->isFollowing($my_user_id);
        // remove email
        unset($user->email);
        return $user;
    }

    public function isFavorited($my_account){
        // find user_id	article_id in favorites table
        $isFavorited = DB::table('favorites')
            ->where('user_id', $my_account)
            ->where('article_id', $this->attributes['id'])
            ->count();
        if ($isFavorited > 0) {
            return true;
        } else {
            return false;
        }
    }
    
}
