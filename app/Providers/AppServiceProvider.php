<?php

namespace App\Providers;

use App\Channel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (App::environment('local')) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 針對單一view
        View::creator('*', function ($view) {

            $channels = Cache::remember('channels', 5, function () {
                return Channel::all();
            });

            $view->with('channels', $channels);
        });

        // 所有view **此方法會在migrate前觸發, 所以測試的時候註解起來
        // View::share('channels', Channel::all());

        // validation rule
        Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }
}
