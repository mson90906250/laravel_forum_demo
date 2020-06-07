<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $response = $this->get(route('thread.show', $this->thread->pathParams()));
        $response->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_with_certain_channel()
    {
        $channel = factory('App\Channel')->create();

        $threadInChannel = factory('App\Thread')->create(['channel_id' => $channel->id]);
        $threadNotInChannel = factory('App\Thread')->create();

        $this->get(route('thread.indexWithChannel', ['channel' => $channel->slug]))
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_username()
    {
        $this->signIn(factory('App\User')->create(['name' => 'Mark Lin']));

        $threadByMark = factory('App\Thread')->create(['user_id' => auth()->id()]);
        $threadNotByMark = factory('App\Thread')->create();

        $this->get(route('thread.index', ['by' => 'Mark Lin']))
            ->assertSee($threadByMark->title)
            ->assertDontSee($threadNotByMark->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        // create three threads with 2, 3, 0 replies respectively
        $threadWithTwoReplies = factory('App\Thread')->create();
        factory('App\Reply', 2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = factory('App\Thread')->create();
        factory('App\Reply', 3)->create(['thread_id' => $threadWithThreeReplies->id]);

        $threadWithoutReplies = $this->thread;

        // when threads being filtered by popularity
        $response = $this->getJson(route('thread.index', ['popular' => 1]))->json();


        // the threads a user gets must be in the order from most replies to least replies
        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_unanswered()
    {
        $threadUnanswered = $this->thread;

        $threadAnswered = factory('App\Thread')->create();
        factory('App\Reply')->create(['thread_id' => $threadAnswered->id]);

        // when threads being filtered by popularity
        $response = $this->getJson(route('thread.index', ['unanswered' => 1]))->json();

        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function a_user_can_get_all_replies_for_a_given_thread()
    {
        $thread = factory('App\Thread')->create();
        factory('App\Reply', 2)->create(['thread_id' => $thread->id]);
        $response = $this->getJson(route('reply.index', $thread->pathParams()))->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }
}
