<?php

namespace App\Traits;

use App\Models\Allocation as Allocation;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use JamesMills\LaravelTimezone\Facades\Timezone;

trait Allocatable
{
    /**
     * @return MorphMany
     */
    public function allocations(): MorphMany
    {
        return $this->morphMany('App\Models\Allocation', 'allocatable');
    }

    /**
     * Make an allocation against the Aloocatable object. Amount is
     *  required. default $date is current date and default phase is 1
     *
     * @param  float  $amount
     * @param  null|string  $date
     * @param  integer  $phase_id
     * @param  bool  $manual_entry
     * @param  bool  $checkIsValuePresent
     * @return Allocation
     */
    public function allocate(
        float $amount,
        string $date = null,
        int $phase_id = 1,
        bool $manual_entry = false,
        bool $checkIsValuePresent = false
    ): ?Allocation {
        // check input
        $date = $date ?? Timezone::convertToLocal(Carbon::now(),'Y-m-d');
        $allocation = null;

        $allowOperation = false;
        if ($checkIsValuePresent) {
            if (!$this->getAllocationByDate($date)) {
                $allowOperation = true;
            }
        } else {
            $allowOperation = true;
        }

        if ($allowOperation) { // @phpstan-ignore-line
            $allocation = Allocation::updateOrCreate(
                [
                    'allocatable_id' => $this->id,
                    'allocatable_type' => get_class($this),
                    'allocation_date' => $date
                ],
                [
                    'phase_id' => $phase_id,
                    'amount' => $amount,
                    'manual_entry' => ($manual_entry ? 1 : null)
                ]
            );
        }

        return $allocation;
    }

    /**
     * Return an allocation for the current Allocatable model for the given
     * date if one exists
     *
     * @param  string  $date
     * @return void
     * @deprecated 9th August 2021
     * Allocations from multiple different sources could all share
     * the same date.
     * Used only in deprecated file from grep search
     * `grep -iR getAllocationByDate ./app`
     * ./app/Http/Livewire/Calculator/__AccountValue.php
     */
    public function getAllocationByDate(string $date)
    {
        $key = 'getAllocationByDate_'.$date;
        $getAllocationByDate = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($getAllocationByDate === null) {
            $getAllocationByDate = Allocation::where(
                [
                    'allocatable_type' => get_class($this),
                    'allocatable_id' => $this->id,
                    'allocation_date' => $date
                ]
            )->first();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $getAllocationByDate);
            }
        }

        return $getAllocationByDate;
    }

    /**
     * Return an allocation for the current Allocatable model for the given
     * date if one exists
     *
     * @param  date|string  $date
     * @return void
     * @deprecated 9th August 2021
     * Allocations from multiple different sources could all share the same
     * date.
     * Does not appear in grep search `grep -iR getAllocationAmount ./app`
     *
     */
    public function getAllocationAmount($date)
    {
        $key = 'getAllocationAmountByDate_'.$date;
        $getAllocationAmountByDate = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($getAllocationAmountByDate === null) {
            $getAllocationAmountByDate = Allocation::where(
                [
                    'allocatable_type' => get_class($this),
                    'allocatable_id' => $this->id,
                    'allocation_date' => $date
                ]
            )->first()->amount;

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $getAllocationAmountByDate);
            }
        }

        return $getAllocationAmountByDate;
    }
}
