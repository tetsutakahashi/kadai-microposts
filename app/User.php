<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
     public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    
    public function favoings()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'microposts_id')->withTimestamps();
    }

    public function favo($micropostId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favoings($micropostId);
        // 自分自身ではないかの確認
        $micropost = \App\Micropost::find($micropostId);
        $its_me = $this->id == $micropost->user_id;
    
        if ($exist || $its_me) {
            // 既にお気に入りしていれば何もしない
            return false;
        } else {
            // 未お気に入りであればお気に入りする
            $this->favoings()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavo($micropostId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favoings($micropostId);
        // 自分自身ではないかの確認
        $micropost = \App\Micropost::find($micropostId);
        $its_me = $this->id == $micropost->user_id;
    
        if ($exist && !$its_me) {
            // 既にお気に入りしていればお気に入りを外す
            $this->favoings()->detach($micropostId);
            return true;
        } else {
            // 未お気に入りであれば何もしない
            return false;
        }
    }
    
    public function is_favoings($micropostId) {
        return $this->favoings()->where('microposts_id', $micropostId)->exists();
    }
}
