<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
        // Ensure compatibility with older MySQL versions (index length)
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
