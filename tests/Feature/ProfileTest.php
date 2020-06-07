<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_other_profile()
    {
        $user = factory('App\User')->create();

        $this->get(route('profile.show', ['user' => $user->name]))
            ->assertSee($user->name);
    }

    /** @test */
    public function a_user_can_view_threads_of_other_users_in_their_profile()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create([
            'user_id' => auth()->id()
        ]);

        $this->get(route('profile.show', ['user' => auth()->user()->name]))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
