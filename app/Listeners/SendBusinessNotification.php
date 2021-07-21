<?php

namespace App\Listeners;

use App\Events\BusinessProcessed;
use App\Mail\SendBusinessNewOwnerNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBusinessNotification
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
     * @param  BusinessProcessed  $event
     * @return void
     */
    public function handle(BusinessProcessed $event)
    {
        if ($event->type == 'newOwner') {
            Mail::to($event->business->owner)->send(new SendBusinessNewOwnerNotification(
                $event->business->owner->name,
                $event->business->name
            ));
        }
    }
}
