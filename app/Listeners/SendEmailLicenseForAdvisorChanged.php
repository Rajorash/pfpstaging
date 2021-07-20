<?php

namespace App\Listeners;

use App\Events\LicenseForAdvisorChanged;
use App\Mail\AdvisorChangedCountOfLicenses;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailLicenseForAdvisorChanged
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LicenseForAdvisorChanged  $event
     * @return void
     */
    public function handle(LicenseForAdvisorChanged $event)
    {
        Mail::to($event->user)->send(new AdvisorChangedCountOfLicenses(
            $event->user,
            $event->author,
            $event->licensesCounter,
            $event->assignedLicenses,
            $event->availableLicenses,
        ));
    }
}
