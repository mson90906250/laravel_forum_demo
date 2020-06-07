<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_get_their_last_reply()
    {
        $user = create('App\User');

        create('App\Reply', ['user_id' => $user->id]);
        sleep(1);
        $newerReply = create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($newerReply->id, $user->fresh()->lastReply->id);
    }
}
