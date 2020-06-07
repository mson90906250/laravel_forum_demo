<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

$factory->define(DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => Str::uuid()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_type' => 'App\User',
        'notifiable_id' => auth()->id(),
        'data'  => ['foo' => 'bar']
    ];
});
