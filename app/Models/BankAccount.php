<?php

namespace App\Models;

use App\Models\Allocation as Allocation;
use App\Models\AllocationPercentage;
use App\Traits\Allocatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BankAccount extends Model
{
    use Allocatable;

    const ACCOUNT_TYPE_REVENUE = 'revenue';
    const ACCOUNT_TYPE_PRETOTAL = 'pretotal';
    const ACCOUNT_TYPE_SALESTAX = 'salestax';
    const ACCOUNT_TYPE_PREREAL = 'prereal';
    const ACCOUNT_TYPE_POSTREAL = 'postreal';

    protected $fillable = ['name', 'type'];
    protected $with = ['flows'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function flows()
    {
        return $this->hasMany(AccountFlow::class, 'account_id');
    }

    public function percentages()
    {
        return $this->hasMany(AllocationPercentage::class, 'bank_account_id');
    }

    public static function type_list()
    {
        return [
            1 => self::ACCOUNT_TYPE_REVENUE,
            2 => self::ACCOUNT_TYPE_PRETOTAL,
            3 => self::ACCOUNT_TYPE_SALESTAX,
            4 => self::ACCOUNT_TYPE_PREREAL,
            5 => self::ACCOUNT_TYPE_POSTREAL
        ];
    }

    /*
        public function allocations()
        {
            return $this->morphMany('App\Models\Allocation', 'allocatable');
        }
    */
    /**
     * @param  null  $phase_id
     * @return mixed
     */
    public function getAllocationPercentages($phase_id = null)
    {
        $key = 'getAllocationPercentages_'.$phase_id.'|'.$this->id;

        $getAllocationPercentages = Cache::get($key);

        if ($getAllocationPercentages === null) {
            if ($phase_id) {
                $getAllocationPercentages = AllocationPercentage
                    ::where('bank_account_id', '=', $this->id)
                    ->where('phase_id', '=', $phase_id)
                    ->get();
            } else {
                $getAllocationPercentages = AllocationPercentage
                    ::where('bank_account_id', '=', $this->id)
                    ->get();
            }
            Cache::put($key, $getAllocationPercentages);
        }
        return $getAllocationPercentages;

//        if($phase_id)
//        {
//            return AllocationPercentage::where('bank_account_id', '=', $this->id)->where('phase_id', '=', $phase_id)->get();
//        }
//
//        return AllocationPercentage::where('bank_account_id', '=', $this->id)->get();

    }

    /**
     * Get all percentages for the current business on given phase
     *
     * @param $phaseId integer
     * @return array
     *  [<account_type> =>
     *      [
     *          <account_id> => <percentage_value>
     *      ]
     *  ]
     */
    public function getAllAllocationPercentages($phaseId)
    {
        $key = 'getAllAllocationPercentages_'.$phaseId.'_'.$this->business_id;

        $getAllAllocationPercentages = Cache::get($key);

        if ($getAllAllocationPercentages === null) {
            $getAllAllocationPercentages = BankAccount::where('business_id', $this->business_id)
                ->with('percentages', function ($query) use ($phaseId) {
                    return $query->where('phase_id', $phaseId);
                })
                ->get()
                ->mapToGroups(function ($item, $key) {
                    return [
                        $item->type => [
                            'id' => $item->id,
                            'val' => count($item->percentages) ? $item->percentages[0]->percent : null
                        ]
                    ];
                })->map(function ($a_item) {
                    return array_column(collect($a_item)->toArray(), 'val', 'id');
                })->toArray();
            Cache::put($key, $getAllAllocationPercentages, now()->addMinutes(10));
        }

        return $getAllAllocationPercentages;
    }

    /**
     * Return the sum of allocations for the given date and current account
     *
     * @param $date
     * @param $phaseId
     * @return mixed
     */
    public function getAllocationsTotalByDate($date, $phaseId)
    {
        $key = 'getAllocationsTotalByDate_'.$date.'_'.$phaseId;

//        $getAllocationsTotalByDate = Cache::get($key);

//        if ($getAllocationsTotalByDate === null) {
        $getAllocationsTotalByDate = AccountFlow::where('account_id', $this->id)
            ->with('allocations', function ($query) use ($date, $phaseId) {
                return $query->where('allocation_date', $date)
                    ->where('phase_id', $phaseId);
            })
            ->get()
            ->map(function ($item) {
                return collect($item->toArray())
                    ->only('negative_flow', 'allocations')
                    ->all();
            })
            ->map(function ($a_item) {
                return count($a_item['allocations']) > 0
                    ? $a_item['negative_flow']
                        ? $a_item['allocations'][0]['amount'] * -1
                        : $a_item['allocations'][0]['amount']
                    : 0;
            })->sum();
//            Cache::put($key, $getAllocationsTotalByDate);
//        }

        return $getAllocationsTotalByDate;
    }

    /**
     * Get revenue (income) for whole business on a given date
     *
     * @param $businessId integer
     * @param $date       string
     * @return mixed
     */
    public function getRevenueByDate($businessId, $date)
    {
        $key = 'getRevenueByDate_'.$businessId.'_'.$date;
        $getRevenueByDate = Cache::get($key);

        if ($getRevenueByDate === null) {
            $getRevenueByDate = self::where('type', 'revenue')->where('business_id', $businessId)
                ->with('allocations', function ($query) use ($date) {
                    return $query->where('allocation_date', $date);
                })
                ->get()
                ->map(function ($item) {
                    return collect($item->toArray())
                        ->only('allocations')
                        ->all();
                })
                ->map(function ($a_item) {
                    return count($a_item['allocations']) > 0
                        ? $a_item['allocations'][0]['amount']
                        : 0;
                })->sum();
            Cache::put($key, $getRevenueByDate, now()->addMinutes(10));
        }

        return $getRevenueByDate;
    }

    /**
     * Get the amount on Transfer In for different account types
     *
     * @param $date     string
     * @param $phase_id integer
     * @return float|int
     */
    public function getTransferAmount($date, $phase_id)
    {
        $percents = $this->getAllAllocationPercentages($phase_id);
        $income = $this->getRevenueByDate($this->business_id, $date);
        $amount = 0;

        // NSP = $income / ($percents['salestax'][<account id>] / 100 + 1)
        // Tax amount = $income - NSP
        switch ($this->type) {
            case 'salestax': // Tax amt
                $amount = ($income > 0)
                    ? round($income - $income / ($percents[$this->type][$this->id] / 100 + 1), 4)
                    : 0;
                break;

            case 'pretotal':
                $salestax = data_get($percents, 'salestax');
                $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
                $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;
                $amount = (is_numeric($percents[$this->type][$this->id]))
                    ? round($nsp * ($percents[$this->type][$this->id] / 100), 4)
                    : 0;
                break;

            case 'prereal':
                $prereal = $this->getPrePrereal($income, $percents);

                $amount = (is_numeric($percents[$this->type][$this->id]))
                    ? round($prereal * ($percents[$this->type][$this->id] / 100), 4)
                    : 0;
                break;

            case 'postreal':
                $prereal = $this->getPrePrereal($income, $percents);
                $prereal_percents = array_sum($percents['prereal']);

                // Real Revenue = $prereal - $prereal * ($prereal_percents / 100)
                $amount = (is_numeric($percents[$this->type][$this->id]))
                    ? round(($prereal - $prereal * ($prereal_percents / 100)) * ($percents[$this->type][$this->id] / 100),
                        4)
                    : 0;
                break;
        }

        return $amount;
    }

    /**
     * Get the value of revenue after excluding sales tax and saving to drip account
     *
     * @param $income
     * @param $percents
     * @return float|int
     */
    private function getPrePrereal($income, $percents)
    {
        $salestax = data_get($percents, 'salestax');
        $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
        $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;

        $pretotal = data_get($percents, 'pretotal');
        $pretotal = count($pretotal) > 0 ? $pretotal[key($pretotal)] : null;
        $pretotal_amt = (is_numeric($pretotal))
            ? round($nsp * ($pretotal / 100), 4)
            : 0;

        return $nsp - $pretotal_amt;
    }

    /**
     * Return the tax rate for the account (if it has one)
     */
    public function taxRate()
    {
        return $this->hasOne(TaxRate::class);
    }

    /**
     * returns true if the account type should be able to be deleted
     *
     * @return boolean
     */
    public function isDeletable()
    {
        $undeletable_types = [
            self::ACCOUNT_TYPE_REVENUE,
            self::ACCOUNT_TYPE_SALESTAX,
        ];

        if( in_array($this->type, $undeletable_types ) )
        {
            return false;
        }

        return true;

    }

}
