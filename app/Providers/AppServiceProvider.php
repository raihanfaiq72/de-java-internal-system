<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $forceHttps = (bool) config('app.force_https');
        if (! $forceHttps) {
            $forceHttps = is_string(config('app.url')) && Str::startsWith(config('app.url'), 'https://');
        }

        if ($forceHttps || config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
