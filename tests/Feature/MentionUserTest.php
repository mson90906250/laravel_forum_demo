<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_user_in_replies_will_receive_notification()
    {
        $mark = create('App\User', ['name' => 'MarkLin']);
        $this->signIn($mark);

        $jane = create('App\User', ['name' => 'JaneChen']);

        $reply = make('App\Reply', [
            'body' => 'Hi @JaneChen and @TerryYang !'
        ]);

        $this->postJson(
                route('reply.store', $reply->thread->pathParams()),
                $reply->toArray()
            );

        $this->assertCount(1, $jane->notifications);

    }

    /** @test */
    public function use_api_to_get_username_according_to_the_name_param()
    {
        create('App\User', ['name' => 'MarkLin']);
        create('App\User', ['name' => 'MarkLin2']);
        create('App\User', ['name' => 'MuskLin']);

        $result = $this->getJson('/api/users?name=' . 'Mark');

        $this->assertCount(2, $result->json());
    }
}
