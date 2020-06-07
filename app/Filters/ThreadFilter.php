<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ThreadFilter extends Filter
{
    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * filter by username
     *
     * @param array $rule
     * @return void
     */
    public function by($username)
    {
        return $this->builder->whereHas('owner', function ($query) use ($username) {
            $query->where([
                ['name', 'like', sprintf('%%%s%%', $username)]
            ]);
        });
    }

    /**
     * filtered by popularity(replies_count)
     *
     * @return Builder
     */
    public function popular()
    {
        //將orderBy裡的created_at去掉
        $this->builder->getQuery()->orders = [];
        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * filtered by
     *
     * @return Builder
     */
    public function unanswered()
    {
        //將orderBy裡的created_at去掉
        $this->builder->getQuery()->orders = [];
        return $this->builder->doesntHave('replies');
    }
}
