<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UserAvatarController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $oldAvatar = $user->avatar_path;

        $request->validate([
            'avatar' => ['required', 'image']
        ]);

        auth()->user()->update([
            'avatar_path' => $request->file('avatar')->store('avatars', 'public')
        ]);

        Storage::disk('public')->delete($oldAvatar);

        return response([], 204);
    }
}
