<?php

namespace App\Listeners;

use App\Events\ThreadReceiveNewReply;

class NotifySubscribers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceiveNewReply  $event
     * @return void
     */
    public function handle(ThreadReceiveNewReply $event)
    {
        $reply = $event->reply;

        $reply->thread->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each
            ->notify($reply);
    }
}
