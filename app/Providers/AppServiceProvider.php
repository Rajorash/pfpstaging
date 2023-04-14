<?php

namespace App\Providers;

use App\Models\LicensesForAdvisors;
use App\Observers\LicensesForAdvisorsObserver;
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
        LicensesForAdvisors::observe(LicensesForAdvisorsObserver::class);
        \Debugbar::disable();
    }
}
