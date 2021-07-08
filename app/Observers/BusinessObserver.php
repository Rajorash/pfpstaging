<?php

namespace App\Observers;

use App\Models\Business;
use App\Models\Phase;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;

class BusinessObserver
{

    /**
     * Function to run upon successful creation of a business
     *
     */
    public function created(Business $business)
    {

        $this->initialisePhases($business);

        // clear the cached all businesses object
        Cache::forget('Business_all');

    }

    /**
     * On business creation, initialise default phase setup.
     *
     * @param [type] $business
     * @return void
     */
    private function initialisePhases($business)
    {
        // create empty phases and assign to the business, each 3 months apart on end_date
        for ($i=0; $i < Phase::DEFAULT_PHASE_COUNT; $i++) {
            # code...
            $phase = new Phase;
            $phase->business_id = $business->id;
            $phase->end_date = Carbon::now()->addMonths(3 * $i);
            $phase->save();
        }

    }

}

