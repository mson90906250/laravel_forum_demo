<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    public function cacheKey()
    {
        return env('APP_ENV') === 'testing' ? 'test_trending_threads' : 'trending_threads';
    }

    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 9));
    }

    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => route('thread.show', $thread->pathParams())
        ]));
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}
