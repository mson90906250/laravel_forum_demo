<?php

namespace App\Http\Controllers;

use App\Reply;

class BestReplyController extends Controller
{
    protected function store(Reply $reply)
    {
        tap($reply->thread, function ($thread) use ($reply) {

            $this->authorize('update', $thread);

            $thread->markBestReply($reply);
        });
    }
}
