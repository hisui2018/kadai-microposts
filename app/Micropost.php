<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Micropost as Authenticatable;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites_follow', 'user_id', 'content_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'favorites_follow', 'content_id', 'user_id')->withTimestamps();
    }
    
    public function follow($favoritesId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($favoritesId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $favoritesId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($favoritesId);
            return true;
        }
    }
    
    public function unfollow($favoritesId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($favoritesId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $favoritesId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($favoritesId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($favoritesId) {
        return $this->followings()->where('favorites_id', $favoritesId)->exists();
    }

}
