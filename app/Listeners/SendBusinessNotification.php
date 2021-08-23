<?php

namespace App\Listeners;

use App\Events\BusinessProcessed;
use App\Mail\SendBusinessDeleteNotification;
use App\Mail\SendBusinessLicenseState;
use App\Mail\SendBusinessNewOwnerNotification;
use App\Mail\SendBusinessCollaborateNotification;
use App\Mail\SendBusinessCollaborateRevokeNotification;
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
        if ($event->type == 'collaboration') {
            Mail::to($event->business->collaboration->advisor->user)->send(new SendBusinessCollaborateNotification(
                $event->business->collaboration->advisor->user->name,
                $event->business->name,
                'Collaboration for you',
                "Business <b>&quot;".$event->business->name."&quot;</b> linked to you by ".$event->business->collaboration->advisor->user->name
            ));
        }

        if ($event->type == 'collaborationDelete') {
            Mail::to($event->business->collaboration->advisor->user)->send(new SendBusinessCollaborateNotification(
                $event->business->collaboration->advisor->user->name,
                $event->business->name,
                'Collaboration was rejected',
                "Business <b>&quot;".$event->business->name."&quot;</b> was unlinked to you by ".$event->business->collaboration->advisor->user->name
            ));
        }

        if ($event->type == 'collaborationRevoke') {
            Mail::to($event->business->collaboration->advisor)->send(new SendBusinessCollaborateRevokeNotification(
                $event->business->collaboration->advisor->name,
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
        if ($event->type == 'activeLicense') {
            //todo: check all relations and refactored it for all related users

            Mail::to($event->business->owner)->send(new SendBusinessLicenseState(
                $event->business,
                $event->user,
                'License number '
                .$event->business->license->account_number
                .' was active for business <b>&quot;'.$event->business->name.'&quot;</b> by you',
                'License was active for business <b>&quot;'.$event->business->name.'&quot;</b>'
            ));

            Mail::to($event->user)->send(new SendBusinessLicenseState(
                $event->business,
                $event->user,
                'License number '
                .$event->business->license->account_number
                .' was active for business <b>&quot;'.$event->business->name.'&quot;</b> by Advisor '
                .'<b>'.$event->user->name.'</b>',
                'License was active for business <b>&quot;'.$event->business->name.'&quot;</b>'
            ));
        }
        if ($event->type == 'inactiveLicense') {
            //todo: check all relations and refactored it for all related users

            Mail::to($event->business->owner)->send(new SendBusinessLicenseState(
                $event->business,
                $event->user,
                'License number '
                .$event->business->license->account_number
                .' was inactive for business <b>&quot;'.$event->business->name.'&quot;</b> by you',
                'License was inactive for business <b>&quot;'.$event->business->name.'&quot;</b>'
            ));

            Mail::to($event->user)->send(new SendBusinessLicenseState(
                $event->business,
                $event->user,
                'License number '
                .$event->business->license->account_number
                .' was inactive for business <b>&quot;'.$event->business->name.'&quot;</b> by Advisor '
                .'<b>'.$event->user->name.'</b>',
                'License was inactive for business <b>&quot;'.$event->business->name.'&quot;</b>'
            ));
        }
    }
}
