<?php

namespace App\Http\Controllers;

use App\Thread;

class LockThreadController extends Controller
{
    /**
     * lock this thread
     *
     * @param Thread $thread
     * @return void
     */
    public function store(Thread $thread)
    {
        $thread->update(['locked' => true]);
    }

    /**
     * unlock this thread
     *
     * @param Thread $thread
     * @return void
     */
    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);
    }
}
