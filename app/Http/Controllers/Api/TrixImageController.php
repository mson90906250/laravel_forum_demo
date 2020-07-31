<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TrixImage;
use Illuminate\Support\Facades\Storage;

class TrixImageController extends Controller
{
    /**
     * store temp image
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:512']
        ]);

        tap($request->file('image')->store('images/trix' , 'public'), function ($filePath) use (&$returnData) {
            $returnData = [
                'cacheKey' => TrixImage::add($filePath),
                'filePath' => $filePath,
                'url' => Storage::url($filePath)
            ];
        });

        return $returnData;
    }

    /**
     * persist images with db
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        TrixImage::persist($request->get('persistList'));
    }

    /**
     * remove images from storage
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
       return Storage::disk('public')->delete($request->get('images'));
    }
}
