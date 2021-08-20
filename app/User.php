<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * このユーザが所有する投稿。（ Postモデルとの関係を定義）
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * このユーザに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount('posts');
    }

    /**
     * このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    /**
     * このユーザがお気に入りの投稿
     */
    public function favorites()
    {
        return $this->belongsToMany(Post::class, 'favorites', 'user_id', 'post_id')->withTimestamps();
    }


    /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }

    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_posts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Post::whereIn('user_id', $userIds);
    }

    /**
     * $postIdで指定された投稿をお気に入りにする。
     *
     * @param  int  $postId
     * @return bool
     */
    public function favorite($postId)
    {
        // すでにお気に入りか確認
        if ($this->is_favorite($postId)) {
            // すでにお気に入りであれば何もしない
            return false;
        } else {
            // お気に入りに追加する
            $this->favorites()->attach($postId);
            return true;
        }
    }

    /**
     * $postIdで指定された投稿をお気に入りから削除する。
     *
     * @param  int  $postId
     * @return bool
     */
    public function unfavorite($postId)
    {
        // すでにお気に入りか確認
        if ($this->is_favorite($postId)) {
            // すでにお気に入りであれば、削除する
            $this->favorites()->detach($postId);
            return true;
        } else {
            // お気に入りでなければ何もしない
            return false;
        }
    }

    /**
     * 指定された $postIdの投稿をこのユーザがお気に入り中であるか調べる。お気に入り中ならtrueを返す。
     *
     * @param  int  $postId
     * @return bool
     */
    public function is_favorite($postId)
    {
        return $this->favorites()->where('post_id', $postId)->exists();
    }
    
}