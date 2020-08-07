<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get($count = 10)
    {
        $list = Redis::zrevrange(
            $this->cacheKey(),
            0,
            ($count-1)
        );

        return $list ? array_map('json_decode', Redis::hmget($this->hashTableKey(), $list)) : [];
    }

    public function score($thread)
    {
        return Redis::zscore($this->cacheKey(), $thread->id);
    }

    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(), 1, $thread->id);

        Redis::hset(
            $this->hashTableKey(),
            $thread->id,
            json_encode([
                'title' => $thread->title,
                'path' => route('thread.show', $thread->pathParams())
            ])
        );
    }

    public function remove($thread)
    {
        Redis::zrem($this->cacheKey(), $thread->id);
        Redis::hdel($this->hashTableKey(), $thread->id);
    }

    public function reset()
    {
        Redis::del($this->cacheKey(), $this->hashTableKey());
    }

    public function cacheKey()
    {
        return env('APP_ENV') === 'testing' ? 'test_trending_threads' : 'trending_threads';
    }

    public function hashTableKey()
    {
        return env('APP_ENV') === 'testing' ? 'test_trending_threads_hash_table' : 'trending_threads_hash_table';
    }
}
