<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function a_notification_will_be_prepared_after_a_subscribed_thread_receives_a_new_reply_posted_by_other()
    {
        $thread = factory('App\Thread')->create()->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        // 自己發佈的reply不會的到通知
        $thread->addReply([
            'user_id' => auth()->id(),
            'body'  => 'test'
        ]);

        $this->assertCount(0, auth()->user()->refresh()->notifications);

        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body'  => 'test'
        ]);

        $this->assertCount(1, auth()->user()->refresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_notifications()
    {
        create(DatabaseNotification::class);

        $response = $this->getJson(route('userNotification.index', ['user' => auth()->user()->name]))->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        create(DatabaseNotification::class);

        $user = auth()->user();

        $notificationId = $user->unreadNotifications->first()->id;

        $this->assertCount(1, $user->unreadNotifications);

        $this->delete(route('userNotification.destroy', ['user' => $user->name, 'notification' => $notificationId]));

        $this->assertCount(0, $user->refresh()->unreadNotifications);
    }
}
