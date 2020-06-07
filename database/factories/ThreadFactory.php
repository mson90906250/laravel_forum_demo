<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Thread;
use App\Channel;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Thread::class, function (Faker $faker) {

    $title = $faker->sentence();

    return [
        'user_id'       => factory(User::class),
        'channel_id'    => factory(Channel::class),
        'title'         => $title,
        'body'          => $faker->paragraph(),
        'slug'          => $title,
        'locked'        => false
    ];
});
