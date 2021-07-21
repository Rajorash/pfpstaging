<?php

namespace App\Listeners;

use App\Events\BusinessProcessed;
use App\Mail\SendBusinessDeleteNotification;
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
        if ($event->type == 'delete') {
            //todo: check all relations and refactored it for all related users

            Mail::to($event->business->owner)->send(new SendBusinessDeleteNotification(
                $event->business->owner->name,
                $event->business->name,
                $event->user->name,
                'Business <b>&quot;'.$event->business->name.'&quot;</b> was deleted by '.$event->user->name
            ));

            Mail::to($event->user)->send(new SendBusinessDeleteNotification(
                $event->business->owner->name,
                $event->business->name,
                $event->user->name,
                'You delete a business <b>&quot;'.$event->business->name.'&quot;</b>'
            ));
        }
    }
}
