<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_favorites_any_replies()
    {
        $this->withExceptionHandling();

        $this->post(route('favorite.store', ['reply' => 1]))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_user_can_favorite_any_replies()
    {
        $this->signIn();

        $reply = factory('App\Reply')->create();

        $this->post(route('favorite.store', ['reply' => $reply->id]));

        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    public function a_user_can_not_favorite_the_same_reply_more_than_one_time()
    {
        $this->signIn();

        $reply = factory('App\Reply')->create();

        try {
            $this->post(route('favorite.store', ['reply' => $reply->id]));
            $this->post(route('favorite.store', ['reply' => $reply->id]));
        } catch(\Exception $e) {
            $this->fail('Did not expect to insert into the same set record twice');
        }


        $this->assertCount(1, $reply->favorites);
    }

     /** @test */
     public function an_authenticated_user_can_unfavorite_a_reply()
     {
         $this->signIn();

         $reply = factory('App\Reply')->create();

         $reply->favorite();

         $this->delete(route('favorite.destroy', ['reply' => $reply->id]));

         $this->assertCount(0, $reply->favorites);

     }
}
