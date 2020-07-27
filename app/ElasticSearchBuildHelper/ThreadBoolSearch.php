<?php

namespace App\ElasticSearchBuildHelper;

use App\Thread;

class ThreadBoolSearch {

    protected $builder;

    public function __construct($search = '')
    {
        $this->search = $search;
        $this->builder = Thread::boolSearch();
    }

    public function size($size)
    {
        $this->builder->size($size);

        return $this;
    }

    public function shouldMatch($matches, $minimunShouldMatch = 1)
    {
        $this->builder->minimumShouldMatch($minimunShouldMatch);

        foreach ($matches as $match) {
            $this->builder->should('match', [$match => $this->search]);
        }

        return $this;
    }

    public function setHighlight($options)
    {
        $this->builder->highlightRaw($options);

        return $this;
    }

    public function highlight($fields)
    {
        foreach ($fields as $field => $option) {
            $this->builder->highlight($field, $option);
        }

        return $this;
    }

    public function rawResult()
    {
        return $this->builder->raw();
    }

}
