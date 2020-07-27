<?php

namespace App\Http\Controllers;

use App\ElasticSearchBuildHelper\ThreadBoolSearch;

class SearchController extends Controller
{
    public function show()
    {
        $buildHelper = new ThreadBoolSearch(request('q'));

        return $buildHelper->shouldMatch(['title', 'body'], 1)
            ->size(5)
            ->setHighlight([
                'pre_tags' => ["<em class='search-highlight'>"],
                'post_tags' => ["</em>"],
                'number_of_fragments' => 1,
                'fragment_size' => 20
            ])
            ->highlight([
                'title' => ['type' => 'fvh', 'matched_fields' => ['title', 'title.chinese']],
                'body'  => ['type' => 'fvh', 'matched_fields' => ['body', 'body.chinese']]
            ])
            ->rawResult();
    }
}
