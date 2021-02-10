<?php

namespace App\Traits;

use App\Models\Allocation as Allocation;
use Carbon\Carbon as Carbon;

trait Allocatable
{
    public function allocations()
    {
        return $this->morphMany('App\Models\Allocation', 'allocatable');
    }

    public function allocate($amount, $date = null, $phase_id = 1)
    {
        // check input

        $date = $date ?? Carbon::now();

        // TODO: change this to updateOrCreate
        $allocation = Allocation::updateOrCreate([
            'allocatable_id' => $this->id,
            'allocatable_type' => get_class($this),
            'allocation_date' => $date
        ],
        [
            'phase_id' => $phase_id,
            'amount' => $amount
        ]);

        return $allocation;
    }

}
