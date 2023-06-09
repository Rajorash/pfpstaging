<?php

namespace App\Providers;

use App\Events\BusinessProcessed;
use App\Events\LicenseForAdvisorChanged;
use App\Events\UserRegistered;
use App\Listeners\SendBusinessNotification;
use App\Listeners\SendEmailLicenseForAdvisorChanged;
use App\Listeners\SendEmailVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],
        UserRegistered::class => [
            SendEmailVerification::class,
        ],
        LicenseForAdvisorChanged::class => [
            SendEmailLicenseForAdvisorChanged::class
        ],
        BusinessProcessed::class => [
            SendBusinessNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}
