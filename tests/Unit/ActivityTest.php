<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;



class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_activity_will_be_created_after_a_new_thread()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id , $thread->id);
    }

    /** @test */
    public function a_activity_will_be_recorded_after_a_reply_was_created()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->create([
            'thread_id' => $thread->id
        ]);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_reply',
            'user_id' => auth()->id(),
            'subject_id' => $reply->id,
            'subject_type' => 'App\Reply'
        ]);
    }

    /** @test */
    public function an_activity_can_be_fetched_grouped_by_date()
    {
        $this->signIn();

        factory('App\Thread', 2)->create(['user_id' => auth()->id()]);

        // activity的created_at是系統生成的 所以必須手動修改時間
        auth()->user()->activities()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        $feeds = Activity::feed(auth()->user());

        $this->assertTrue($feeds->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));

        $this->assertTrue($feeds->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
