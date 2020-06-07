<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $search = sprintf('%s%%', request('name'));

        return User::where('name', 'LIKE', $search)
            ->pluck('name')
            ->take(5)
            ->map(function ($username) {
                return ['name' => $username];
            });
    }
}
