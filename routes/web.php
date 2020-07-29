<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::group([
    'middleware' => 'auth'
], function () {
    //thread
    Route::get('/threads/create', 'ThreadController@create')->name('thread.create');
    Route::post('/threads/create', 'ThreadController@store')->name('thread.store')->middleware('verified');
    Route::patch('/threads/{channel}/{thread}', 'ThreadController@update')->name('thread.update');
    Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy')->name('thread.destroy');

    //reply
    Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store')->name('reply.store');
    Route::delete('/replies/{reply}', 'ReplyController@destroy')->name('reply.destroy');
    Route::patch('/replies/{reply}', 'ReplyController@update')->name('reply.update');

    //favorite
    Route::post('/replies/{reply}/favorite', 'FavoriteController@store')->name('favorite.store');
    Route::delete('/replies/{reply}/favorite', 'FavoriteController@destroy')->name('favorite.destroy');

    //subscriptionThread
    Route::post('/threads/{channel}/{thread}/subscriptions', 'SubscriptionThreadController@store')->name('subscriptionThread.store');
    Route::delete('/threads/{channel}/{thread}/subscriptions', 'SubscriptionThreadController@destroy')->name('subscriptionThread.destroy');

    //userNotification
    Route::get('/profiles/{user}/notifications', 'UserNotificationController@index')->name('userNotification.index');
    Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationController@destroy')->name('userNotification.destroy');

    //bestReply
    Route::post('/thread/{reply}/best', 'BestReplyController@store')->name('bestReply.store');

    //lockThread
    Route::post('/lock-thread/{thread}', 'LockThreadController@store')->name('lockThread.store')->middleware('admin');
    Route::delete('/lock-thread/{thread}', 'LockThreadController@destroy')->name('lockThread.destroy')->middleware('admin');

    // api/userAvatar
    Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store')->name('avatar');

    // api/threadImage
    Route::post('/api/threads/image', 'Api\ThreadImageController@store')->name('threadImage.store');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/threads', 'ThreadController@index')->name('thread.index');
Route::get('/threads/search', 'SearchController@show')->name('search.show');
Route::get('/threads/{channel}', 'ThreadController@index')->name('thread.indexWithChannel');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show')->name('thread.show');

//reply
Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index')->name('reply.index');

//profile
Route::get('/profiles/{user}', 'ProfileController@show')->name('profile.show');

// api/user
Route::get('/api/users', 'Api\UserController@index');


