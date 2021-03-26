<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Business as Business;
use App\Observers\BusinessObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Business::observe(BusinessObserver::class);
        // \Debugbar::disable();
    }
}
