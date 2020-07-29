<?php

namespace App\Http\Controllers\Api;

use App\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ThreadImageController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:512']
        ]);

        tap($request->file('image')->store('images/threads' , 'public'), function ($filePath) use (&$returnData) {
            $returnData = [
                'filePath' => $filePath,
                'url' => Storage::url($filePath)
            ];
        });

        return $returnData;
    }
}
