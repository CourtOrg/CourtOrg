<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// neu von Albums Photos
//use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // neu von Albums Photos
        //Schema::defaultStringLenght(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}