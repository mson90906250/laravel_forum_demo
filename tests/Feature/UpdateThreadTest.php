<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateThreadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn();
    }

    /** @test */
    public function unauthorized_users_can_not_update_threads()
    {
        $thread = create('App\Thread');

        $this->patch(route('thread.update', $thread->pathParams()), [])
            ->assertStatus(403);
    }

    /** @test */
    public function title_and_body_are_required_to_update_threads()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch(route('thread.update', $thread->pathParams()), [])
            ->assertSessionHasErrors(['body', 'title']);
    }

    /** @test */
    public function threads_can_be_updated_by_their_creators()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch(route('thread.update', $thread->pathParams()), [
            'body' => 'Changed body',
            'title' => 'Changed title'
        ]);

        tap($thread->fresh(), function ($thread) {
            $this->assertEquals('Changed body', $thread->body);
            $this->assertEquals('Changed title', $thread->title);
        });
    }
}
