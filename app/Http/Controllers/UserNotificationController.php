<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function index(User $user)
    {
        return auth()->user()->unreadNotifications;
    }

    public function destroy(User $user, $notification)
    {
        auth()->user()->notifications()->findOrFail($notification)->markAsRead();
    }
}
