<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use App\Http\Requests\CreatePostRequest;

/**
 * ReplyController
 */
class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    public function index($channel, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }

    /**
     * store function
     *
     * @param $mixed $channelSlug
     * @param Thread $thread
     * @param Request $request
     * @param CreatePostRequest $request
     * @return array
     */
    public function store($channelSlug, Thread $thread, CreatePostRequest $request)
    {
        if ($thread->locked) {
            return response('This is locked :_:', 422);
        }

        $reply = $thread->addReply([
                'user_id' => auth()->id(),
                'body' => $request->get('body')
            ])
            ->load('owner');

        // 取得該reply為於此thread的哪一頁 以及 目前最新的reply總數
        $count = $thread->replies()
            ->where([
                ['id', '<=', $reply->id]
            ])
            ->count();

        return [
            'reply' => $reply,
            'page'  => floor(($count-1)/10) + 1,
            'count' => $count
        ];

    }

    /**
     * update the reply
     *
     * @param Reply $reply
     * @return void
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $validatedData = request()->validate([
            'body' => ['required', 'spamfree']
        ]);

        $reply->update(['body' => $validatedData['body']]);

        if (request()->expectsJson()) {
            return $reply;
        }
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->wantsJson()) {
            return response('The reply has been deleted!');
        }

        return back();
    }
}
