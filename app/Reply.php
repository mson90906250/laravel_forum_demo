<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

    protected $fillable = ['user_id', 'thread_id', 'body'];
    protected $with = ['owner', 'favorites', 'thread'];
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest']; //當請求要求返回json或array時, 將appends裡的attribute加進去
    protected $touches = ['thread'];  //when this(child model) being created or updated, update the parent(ex: thread) model's updated_at column

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }

    public function wasJustPublished()
    {
        return $this->created_at->addMinute()->gt(Carbon::now());
    }

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    /**
     * get mentioned user's name in the body of replies
     * user's name format: @username
     *
     * ex: 'Hello @TomTsai and @MarkLin' will return ['TomTsai', 'MarkLin']
     *
     * @return array
     */
    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-]+)/', $this->body, $matches);
        return $matches[1];
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }
}
