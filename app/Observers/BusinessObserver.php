<?php

namespace App\Observers;

use App\Models\Business;
use App\Models\Phase;
use Carbon\Carbon as Carbon;

class BusinessObserver
{

    /**
     * Function to run upon successful creation of a business
     *
     */
    public function created(Business $business)
    {
        // create empty phases and assign to the business, each 3 months apart on end_date
        for ($i=0; $i < Phase::DEFAULT_PHASE_COUNT; $i++) {
            # code...
            $phase = Phase::create([
                'business_id' => $business->id,
                'end_date' => Carbon::now()->addMonths(3 * $i)
            ]);
        }

    }

}
