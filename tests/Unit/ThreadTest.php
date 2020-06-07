<?php

namespace Tests\Unit;

use App\Thread;
use Tests\TestCase;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $reply = factory('App\Reply')->create([
            'user_id' => factory('App\User')->create(),
            'thread_id' => $this->thread->id
        ]);

        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    /** @test */
    public function a_thread_has_its_owner()
    {
        $this->assertInstanceOf('App\User', $this->thread->owner);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->signIn();

        $reply = factory('App\Reply')->make([
            'user_id' => 1,
        ]);

        $this->thread->addReply($reply->toArray());

        $this->assertCount(1, $this->thread->replies);

    }

    /** @test */
    public function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn();

        $this->thread
            ->subscribe()
            ->addReply([
                'user_id' => create('App\User')->id,
                'body' => 'foobar'
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_has_its_own_channel()
    {
        $thread = factory('App\Thread')->create();

        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /** @test */
    public function a_thread_has_its_own_path_with_channel_slug()
    {
        $thread = create('App\Thread');

        $this->assertEquals(
            url(sprintf('/threads/%s/%s', $thread->channel->slug, $thread->slug)),
            route('thread.show', $thread->pathParams())
        );
    }

    /** @test */
    public function an_authenticated_user_can_subscribe_threads()
    {
        $thread = factory('App\Thread')->create();

        $thread->subscribe($userId = 1);

        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    /** @test */
    public function a_thread_can_be_unsubscribed()
    {
        $thread = factory('App\Thread')->create();

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertEquals(0, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    /** @test */
    public function it_knows_which_user_subscribes_to_it()
    {
        $this->signIn();

        $thread = factory('App\Thread')->create();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    public function a_thread_knows_whether_an_authenticated_user_read_all_the_replies()
    {
        $this->signIn();

        tap(auth()->user(), function ($user) {

            $this->assertTrue($this->thread->hasUpdatesFor($user));

            $user->read($this->thread);

            $this->assertFalse($this->thread->hasUpdatesFor($user));

        });
    }

    /** @test */
    public function it_can_record_each_visit()
    {
        $thread = make('App\Thread');

        $thread->visits()->reset();

        $this->assertSame(0, $thread->visits()->count());

        $thread->visits()->record();

        $this->assertEquals(1, $thread->visits()->count());

        $thread->visits()->record();

        $this->assertEquals(2, $thread->visits()->count());
    }

    /** @test */
    public function threads_can_be_searched()
    {
        $this->useScoutDriver();

        $search = '後悔莫及';

        create('App\Thread', [], 2);
        create('App\Thread', ['title' => "This is a title with {$search} term"]);
        create('App\Thread', ['body' => "This is a body with {$search} term"]);

        for ($i=0; $i<10; $i++) {
            usleep(200000);
            $results = Thread::search($search)->get();
            if ($results->count() >= 2) break;
        }

        Thread::latest()->take(4)->get()->each->delete();

        $this->assertCount(2, $results);
    }
}
