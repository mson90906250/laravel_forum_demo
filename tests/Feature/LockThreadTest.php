<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LockThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_administrator_can_not_lock_threads()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread');

        $this->post(route('lockThread.store', $thread))
            ->assertStatus(403);

        $this->assertFalse(!! $thread->refresh()->locked);
    }

    /** @test */
    public function an_administrator_can_lock_threads()
    {
        // simulate logging an admin
        $this->signIn(factory('App\User')->state('administrator')->create());

        $thread = create('App\Thread');

        $this->post(route('lockThread.store', $thread));

        $this->assertTrue(!! $thread->refresh()->locked);
    }

    /** @test */
    public function an_administrator_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->state('administrator')->create());

        $thread = create('App\Thread', ['locked' => true]);

        $this->delete(route('lockThread.destroy', $thread));

        $this->assertFalse($thread->refresh()->locked);
    }

    /** @test */
    public function a_reply_should_not_be_published_after_threads_being_locked()
    {
        $this->signIn();

        $thread = create('App\Thread', ['locked' => true]);

        $this->post(route('reply.store', $thread->pathParams()), [
            'user_id' => auth()->id(),
            'body' => 'foobar'
        ])->assertStatus(422);
    }
}
