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
    public function when_a_user_visit_a_thread_the_view_number_of_that_thread_will_be_increased_by_1()
    {
        $thread = create('App\Thread');

        $this->assertCount(0, $this->trending->get());

        $this->get(route('thread.show', $thread->pathParams()));

        $this->assertCount(1, $this->trending->get());

        $this->assertEquals($thread->title, $this->trending->get()[0]->title);
    }
}
