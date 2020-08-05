<?php

namespace Tests\Feature;

use App\Trending;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected $trending;

    protected function setUp() : void
    {
        parent::setUp();

        $this->trending = new Trending();

        $this->trending->reset();
    }

    /** @test */
    public function when_a_user_visit_a_thread_the_score_of_that_thread_will_be_increased_by_1()
    {
        $thread = create('App\Thread');

        $this->assertEquals(0, $this->trending->score($thread));

        $this->get(route('thread.show', $thread->pathParams()));

        $this->assertEquals(1, $this->trending->score($thread));

        $this->assertEquals($thread->title, $this->trending->get()[0]->title);
    }

    /** @test */
    public function its_trending_record_will_be_removed_after_a_thread_being_deleted()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->get(route('thread.show', $thread->pathParams()));

        $this->delete(route('thread.destroy', $thread->pathParams()));

        $this->assertDatabaseMissing('threads', ['slug' => $thread->slug, 'id' => $thread->id]);

        $this->assertFalse($this->trending->score($thread));

        $this->assertCount(0, $this->trending->get());
    }
}
