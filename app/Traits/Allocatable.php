<?php

namespace App\Traits;

use App\Models\Allocation as Allocation;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;

trait Allocatable
{
    /**
     * Returns a collection of all associated allocations.
     *
     * @return Collection
     */
    public function allocations()
    {
        return $this->morphMany('App\Models\Allocation', 'allocatable');
    }

    /**
     * Make an allocation against the Aloocatable object. Amount is
     *  required. default $date is current date and default phase is 1
     *
     * @param  double  $amount
     * @param  date  $date
     * @param  integer  $phase_id
     * @return Allocation
     */
    public function allocate($amount, $date = null, $phase_id = 1)
    {
        // check input

        $date = $date ?? Carbon::now();

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

    /**
     * Return an allocation for the current Allocatable model for the given
     * date if one exists
     *
     * @param  date|string  $date
     * @return void
     */
    public function getAllocationByDate($date)
    {
        $key = 'getAllocationByDate_'.$date;
        $getAllocationByDate = Cache::get($key);

        if ($getAllocationByDate === null) {
            $getAllocationByDate = Allocation::where(
                [
                    'allocatable_type' => get_class($this),
                    'allocatable_id' => $this->id,
                    'allocation_date' => $date
                ]
            )->first();

            Cache::put($key, $getAllocationByDate);
        }

        return $getAllocationByDate;
    }

    /**
     * Return an allocation for the current Allocatable model for the given
     * date if one exists
     *
     * @param  date|string  $date
     * @return void
     */
    public function getAllocationAmount($date)
    {
        $key = 'getAllocationAmountByDate_'.$date;
        $getAllocationAmountByDate = Cache::get($key);

        if ($getAllocationAmountByDate === null) {
            $getAllocationAmountByDate = Allocation::where(
                [
                    'allocatable_type' => get_class($this),
                    'allocatable_id' => $this->id,
                    'allocation_date' => $date
                ]
            )->first()->amount;

            Cache::put($key, $getAllocationAmountByDate);
        }

        return $getAllocationAmountByDate;
    }
}
