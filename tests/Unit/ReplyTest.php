<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use App\Thread;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test  */
    public function a_reply_has_owner()
    {
        $reply = factory(Reply::class)->create([
            'user_id' => factory(User::class)->create(),
            'thread_id' => factory(Thread::class)->create([]),
        ]);

        $this->assertInstanceOf(User::class, $reply->owner);
    }

    /** @test */
    public function a_reply_know_whether_it_was_just_published()
    {
        $this->signIn();

        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subHour();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_can_detect_mentioned_users()
    {
        $reply = new \App\Reply([
            'body' => 'Hello @MarkLin and @TomDoe'
        ]);

        $this->assertEquals(['MarkLin', 'TomDoe'], $reply->mentionedUsers());
    }

    /** @test */
    public function it_wraps_mentioned_username_in_anchor_tags()
    {
        // 符號 "\u{008D}" 是種non-printable 符號 目的是用來方便區隔文字
        $reply = new \App\Reply([
            'body' => 'Hello @MarkLin' . "\u{008D}"
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/MarkLin">@MarkLin</a>',
            $reply->body
        );
    }

    /** @test */
    public function it_knows_whether_it_is_the_best_reply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->refresh()->isBest());
    }
}
