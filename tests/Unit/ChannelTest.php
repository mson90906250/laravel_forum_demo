<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
   public function a_channel_consists_of_threads()
   {
        $channel = factory('App\Channel')->create();
        $thread = factory('App\Thread')->create(['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
   }
}
