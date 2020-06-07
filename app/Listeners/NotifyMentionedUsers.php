<?php

namespace App\Listeners;

use App\User;
use App\Events\ThreadReceiveNewReply;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceiveNewReply  $event
     * @return void
     */
    public function handle(ThreadReceiveNewReply $event)
    {
        $reply = $event->reply;

        $notification = new YouWereMentioned($reply);

        $users = User::whereIn('name', $reply->mentionedUsers())->get()
            ->each(function ($user) use ($notification) {
                $user->notify($notification);
            });
    }
}
