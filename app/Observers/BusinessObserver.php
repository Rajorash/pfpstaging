<?php

namespace App\Observers;

use App\Business;
use App\Phase;

class BusinessObserver
{
    /**
     * Function to run upon successful creation of a business
     *
     */
    public function created(Business $business)
    {
        // create an empty phase and assign to the business
        $phase = Phase::create([
            'business_id' => $business->id
        ]);

    }
}
