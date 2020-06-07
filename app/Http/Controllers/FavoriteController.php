<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Reply $reply)
    {
        $reply->favorite();

        if (request()->wantsJson()) {
            return response(['status' => 'success']);
        }

        return back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();

        if (request()->wantsJson()) {
            return response(['status' => 'success']);
        }

        return back();
    }
}
