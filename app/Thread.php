<?php

namespace App;

use App\Visit;
use Illuminate\Support\Str;
use App\Filters\ThreadFilter;
use Laravel\Scout\Searchable;
use App\Events\ThreadReceiveNewReply;
use Illuminate\Database\Eloquent\Model;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Builder;

class Thread extends Model
{
    use RecordsActivity, Searchable, CustomSearch;

    protected $fillable = ['user_id', 'title', 'body', 'channel_id', 'slug', 'best_reply_id', 'locked'];
    protected $with = ['channel', 'owner'];
    protected $appends = ['isSubscribedTo'];
    protected $visits;
    protected $casts = [
        'locked' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount', function ($builder) {
            $builder->withCount('replies');
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'path' => '/threads/' . $this->channel->slug . '/' . $this->slug
        ];
    }

    public function pathParams()
    {
        return [
            'channel'   => $this->channel->slug,
            'thread'    => $this->slug
        ];
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class)
                    ->with('owner');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function subscriptions()
    {
        return $this->hasMany('App\SubscriptionThread');
    }

    public function visits()
    {
        return $this->visits ?? $this->visits = new Visit($this);
    }

    /**
     * add a new reply to this thread
     *
     * @param App\Reply $reply
     * @return App\Reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceiveNewReply($reply));

        return $reply;
    }

    /**
     * subscribe this thread
     *
     * @param int $userId
     * @return Thread
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * unsubscribe
     *
     * @param [type] $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId?: auth()->id())
            ->delete();
    }

    /**
     * to check if the auth has the subscription to itself
     *
     * @return boolean
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }

    /**
     * to detect if it has something new for the target user
     *
     * @param User $user
     * @return boolean
     */
    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);
        return $this->updated_at > cache($key);
    }

    /**
     * mark a reply as its best reply
     *
     * @param Reply $reply
     * @return void
     */
    public function markBestReply(Reply $reply)
    {
        $this->best_reply_id = $reply->id;

        $this->save();
    }

   /**
    * filter
    *
    * @param Builder $builder
    * @param ThreadFilter $filter
    * @return void
    */
    public function scopeFilter(Builder $builder, ThreadFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = urlencode($value);

        $latestId = static::latest()->value('id');

        $this->attributes['slug'] = $latestId ? "{$slug}-{$latestId}" : $slug;
    }
}
