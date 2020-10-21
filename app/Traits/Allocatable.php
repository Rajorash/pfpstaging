<?php

namespace App\Traits;

use App\Allocation as Allocation;
use Carbon\Carbon as Carbon;

trait Allocatable
{
    public function allocations()
    {
        return $this->morphMany('App\Allocation', 'allocatable');
    }

    public function allocate($amount, $phase_id = 1, $date = null)
    {
        // check input

        $date = $date ?? Carbon::now();

        $allocation = new Allocation();
        $allocation->amount = $amount;
        $allocation->phase_id = $phase_id;
        $allocation->allocatable_id = $this->id;
        $allocation->allocatable_type = get_class($this);
        $allocation->allocation_date = $date;

        $allocation->save();
    }

}
