<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function thread_creators_can_mark_the_best_reply ()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('bestReply.store', ['reply' => $replies[1]->id]));

        $this->assertTrue($replies[1]->refresh()->isBest());
    }

    /** @test */
    public function only_the_thread_creator_can_mark_the_best_reply()
    {
        $this->withExceptionHandling();

        $thread = create('App\Thread');

        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->signIn(create('App\User'));

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('bestReply.store', ['reply' => $replies[1]->id]))
            ->assertStatus(403);

        $this->assertFalse($replies[1]->refresh()->isBest());
    }

    /** @test */
    public function when_a_reply_marked_as_the_best_reply_being_deleted_its_thread_should_react_to_this()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->assertEquals($reply->id, $reply->thread->fresh()->best_reply_id);

        $this->deleteJson(route('reply.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);
    }
}
