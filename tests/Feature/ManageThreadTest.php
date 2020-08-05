<?php

namespace Tests\Feature;

use App\Thread;
use App\Activity;
use Tests\TestCase;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageThreadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->mock(Recaptcha::class, function ($m) {
            $m->shouldReceive('passes')->andReturn(true);
        });
    }

    /** @test */
    public function an_unauthenticated_user_may_not_create_a_thread()
    {
        $this->withExceptionHandling();

        $this->get(route('thread.create'))
            ->assertRedirect(route('login'));

        $this->post(route('thread.store', ['thread' => 1]), [])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_create_a_thread()
    {
        $this->signIn();

        $thread = make('App\Thread');

        $reponse =  $this->post(route('thread.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->get($reponse->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->withExceptionHandling();

        $user = factory('App\User')->create();
        $this->actingAs($user);

        $thread = factory('App\Thread')->make(['title' => NULL]);

        $this->post(route('thread.store'), $thread->toArray())
            ->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->withExceptionHandling();
        $this->actingAs(factory('App\User')->create());

        $thread = factory('App\Thread')->make(['body' => NULL]);

        $this->post(route('thread.store'), $thread->toArray())
            ->assertSessionHasErrors(['body']);
    }

    /** @test */
    public function a_thread_requires_a_valid_channel_id()
    {
        $this->withExceptionHandling();
        $this->signIn();

        factory('App\Channel', 2)->create();

        $thread = factory('App\Thread')->make(['channel_id' => NULL]);
        $this->post(route('thread.store'), $thread->toArray())
            ->assertSessionHasErrors(['channel_id']);

        $thread = factory('App\Thread')->make(['channel_id' => 999]);
        $this->post(route('thread.store'), $thread->toArray())
            ->assertSessionHasErrors(['channel_id']);
    }

    /** @test */
    public function a_thread_requires_recaptcha_veryfication()
    {
        unset(app()[Recaptcha::class]);

        $this->withExceptionHandling();

        $this->signIn();

        $thread = make('App\Thread');

        $this->post(route('thread.store'), $thread->toArray() + ['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_slug_must_be_unique()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Foo Title', 'Slug' => 'Foo Title']);

        $this->post(route('thread.store'), $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertTrue(Thread::where('slug', "foo-title")->exists());
        $this->assertTrue(Thread::where('slug', "foo-title-{$thread->id}")->exists());
    }

    /** @test */
    public function unauthorized_users_can_not_delete_threads()
    {
        $thread = factory('App\Thread')->create();

        $this->withExceptionHandling()
            ->delete(route('thread.destroy', $thread->pathParams()))
            ->assertRedirect(route('login'));

        $this->signIn();
        $this->delete(route('thread.destroy', $thread->pathParams()))
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create(['user_id' => auth()->id()]);
        $reply = factory('App\Reply')->create(['thread_id' => $thread->id]);

        $reply->favorite();
        $favorite = $reply->favorites()->first();

        $response = $this->json('DELETE', route('thread.destroy', $thread->pathParams()));
        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
        $this->assertEquals(0, Activity::count());
    }
}
