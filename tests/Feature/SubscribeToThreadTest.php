<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscribeToThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_subscribe_to_threads()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create();

        //訂閲
        $this->post(route('subscriptionThread.store', array_merge($thread->pathParams())));

        $this->assertCount(1, $thread->refresh()->subscriptions);
    }

    /** @test */
    public function an_authenticated_user_can_unsubscribe_to_threads()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create();

        $thread->subscribe();

        $this->delete(route('subscriptionThread.destroy', array_merge($thread->pathParams())));

        $this->assertCount(0, $thread->subscriptions);
    }


}
