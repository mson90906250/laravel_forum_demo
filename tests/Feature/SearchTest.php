<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        $this->useScoutDriver();

        $search = 'foobar';

        create('App\Thread', [], 2);
        create('App\Thread', ['body' => "A content with {$search} term"]);
        create('App\Thread', ['title' => "A title with {$search} term"]);

        for ($i=0; $i<10; $i++) {
            usleep(200000);
            $results = $this->getJson(route('search.show', ['q' => $search]))->json();
            if (count($results['hits']['hits']) >= 2) break;
        }

        Thread::latest()->take(4)->get()->each->delete();

        $this->assertCount(2, $results['hits']['hits']);
    }
}
