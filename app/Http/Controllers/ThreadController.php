<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use App\Trending;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use App\Filters\ThreadFilter;

class ThreadController extends Controller
{
    /**
     * index
     *
     * @param Channel $channel
     * @param ThreadFilter $filter
     * @param Trending $trending
     * @return mixed
     */
    public function index(Channel $channel, ThreadFilter $filter, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filter);

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('thread.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('thread.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Recaptcha $recaptcha)
    {
        $validatedData = $request->validate([
            'channel_id'    => ['required', 'integer', 'exists:channels,id'],
            'title'         => ['required', 'spamfree'],
            'body'          => ['required', 'spamfree'],
            'g-recaptcha-response' => ['required', $recaptcha]
        ]);

        $thread = Thread::create([
            'user_id'       => auth()->id(),
            'channel_id'    => $validatedData['channel_id'],
            'title'         => $validatedData['title'],
            'body'          => $validatedData['body'],
            'slug'          => $validatedData['title'],
        ]);

        return redirect(route('thread.show', $thread->pathParams()))
            ->with('flash', 'Your thread has been created!');
    }

    /**
     * show
     *
     * @param mixed $channelSlug
     * @param Thread $thread
     * @param Trending $trending
     * @return mixed
     */
    public function show($channelSlug, Thread $thread, Trending $trending)
    {
        //record the last view time
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->visits()->record();

        return view('thread.show', compact('thread'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $channel
     * @param  \App\Thread  $thread
     * @return Thread
     */
    public function update(Request $request, $channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $validatedData = $request->validate([
            'title'         => ['required', 'spamfree'],
            'body'          => ['required', 'spamfree']
        ]);

        $thread->update($validatedData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread, Trending $trending)
    {
        $this->authorize('update', $thread);

        $trending->remove($thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response('', 204);
        }

        return redirect(route('thread.index'));
    }

    protected function getThreads($channel, $filter)
    {
        $threads = Thread::latest();

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->filter($filter)
            ->paginate(10)
            ->withQueryString();
    }
}
