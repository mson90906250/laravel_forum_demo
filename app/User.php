<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email'
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
     * get key for query
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany('App\Thread')->latest();
    }

    public function activities()
    {
        return $this->hasMany('App\Activity');
    }

    public function replies()
    {
        return $this->hasMany('App\Reply')->latest();
    }

    public function lastReply()
    {
        return $this->hasOne('App\Reply')->latest();
    }

    public function getAvatarPathAttribute($avatar)
    {
        return Storage::url($avatar ?? 'avatars/default.png');
    }

    public function read($thread)
    {
        $key = $this->visitedThreadCacheKey($thread);
        cache()->forever($key, \Carbon\Carbon::now());
    }

    /**
     * Check if this user is an administrator
     * Notice that right now we just hard code this method
     * It's up to you to migrate this method to use database
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return in_array($this->name, ['admin']);
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf('users.%s.thread.%s', $this->id, $thread->id);
    }
}
