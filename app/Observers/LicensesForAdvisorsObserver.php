<?php

namespace App\Observers;

use App\Models\LicensesForAdvisors;

class LicensesForAdvisorsObserver
{
    public $afterCommit = true;

    /**
     * Handle the LicensesForAdvisors "created" event.
     *
     * @param  \App\Models\LicensesForAdvisors  $licensesForAdvisors
     * @return void
     */
    public function created(LicensesForAdvisors $licensesForAdvisors)
    {
//        dd($licensesForAdvisors);
    }

    /**
     * Handle the LicensesForAdvisors "updated" event.
     *
     * @param  \App\Models\LicensesForAdvisors  $licensesForAdvisors
     * @return void
     */
    public function updated(LicensesForAdvisors $licensesForAdvisors)
    {
    }

    /**
     * Handle the LicensesForAdvisors "deleted" event.
     *
     * @param  \App\Models\LicensesForAdvisors  $licensesForAdvisors
     * @return void
     */
    public function deleted(LicensesForAdvisors $licensesForAdvisors)
    {
        //
    }

    /**
     * Handle the LicensesForAdvisors "restored" event.
     *
     * @param  \App\Models\LicensesForAdvisors  $licensesForAdvisors
     * @return void
     */
    public function restored(LicensesForAdvisors $licensesForAdvisors)
    {
        //
    }

    /**
     * Handle the LicensesForAdvisors "force deleted" event.
     *
     * @param  \App\Models\LicensesForAdvisors  $licensesForAdvisors
     * @return void
     */
    public function forceDeleted(LicensesForAdvisors $licensesForAdvisors)
    {
        //
    }
}
