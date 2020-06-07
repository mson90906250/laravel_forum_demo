<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_unauthenticated_user_may_not_reply()
    {
        $thread = factory('App\Thread')->create();

        $this->withExceptionHandling()
            ->post(route('reply.store', $thread->pathParams()), [])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_reply_to_a_thread()
    {
        //模擬登入
        $user = factory('App\User')->create();
        $this->be($user);

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make();

        $this->post(route('reply.store', $thread->pathParams()), $reply->toArray());

        $this->get(route('thread.show', $thread->pathParams()))
            ->assertSee($reply->body);

    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling();
        $user = factory('App\User')->create();
        $this->be($user);

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make(['body' => NULL]);

        $this->postJson(route('reply.store', $thread->pathParams()), $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function an_unauthorized_user_can_not_delete_replies()
    {
        $reply = factory('App\Reply')->create();

        $this->withExceptionHandling()
            ->delete(route('reply.destroy', ['reply' => $reply->id]))
            ->assertRedirect(route('login'));

        $this->signIn();

        $this->withExceptionHandling()
            ->delete(route('reply.destroy', ['reply' => $reply->id]))
            ->assertStatus(403);
    }

    /** @test */
    public function an_authorized_user_can_delete_replies()
    {
        $this->signIn();

        $reply = factory('App\Reply')->create(['user_id' => auth()->id()]);

        $this->delete(route('reply.destroy', ['reply' => $reply->id]))
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /** @test */
    public function an_unauthorized_user_can_not_udpate_replies()
    {
        $reply = factory('App\Reply')->create();

        $this->withExceptionHandling()
            ->patch(route('reply.update', ['reply' => $reply->id]))
            ->assertRedirect(route('login'));

        $this->signIn();

        $this->withExceptionHandling()
            ->patch(route('reply.update', ['reply' => $reply->id]))
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = factory('App\Reply')->create(['user_id' => auth()->id()]);

        $updateStr = 'this reply had been updated!!';
        $this->patch(route('reply.update', ['reply' => $reply]), ['body' => $updateStr]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updateStr]);
    }

    /** @test */
    public function a_reply_must_not_contain_spam()
    {
        $this->signIn();
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $this->postJson(
            route('reply.store', $thread->pathParams()),
            ['body' => 'Dirty word']
        )
        ->assertStatus(422);

    }

    /** @test */
    public function a_user_may_not_post_multi_replies_per_one_minute()
    {
        $this->signIn();
        $this->withExceptionHandling();

        $reply = make('App\Reply', ['user_id' => auth()->id()]);

        $this->post(route('reply.store', $reply->thread->pathParams()), $reply->toArray())
            ->assertStatus(200);

        $this->post(route('reply.store', $reply->thread->pathParams()), $reply->toArray())
            ->assertStatus(429);
    }
}
