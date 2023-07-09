<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable=['body','article_id','user_id'];
    protected $hidden=['user_id','article_id'];

    public function getAuthor($my_user_id)
    {
        $user = User::find($this->attributes['user_id']);
        if ($my_user_id){
            $user->isFollowing($my_user_id);
        }else{
            $user->following = false;
        }
        // remove email
        unset($user->email);
        return $user;
    }
}
