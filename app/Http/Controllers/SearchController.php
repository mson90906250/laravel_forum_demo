<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        $search = request('q');

        return Thread::boolSearch()
            ->should('match', ['title' => $search])
            ->should('match', ['body' => $search])
            ->size(5)
            ->highlightRaw([
                'pre_tags' => ["<em class='search-highlight'>"],
                'post_tags' => ["</em>"],
                'number_of_fragments' => 1,
                'fragment_size' => 20
            ])
            ->highlight('title', ['type' => 'fvh', 'matched_fields' => ['title', 'title.chinese']])
            ->highlight('body', ['type' => 'fvh', 'matched_fields' => ['body', 'body.chinese']])
            ->raw();
    }
}
