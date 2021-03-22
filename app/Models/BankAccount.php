<?php

namespace App\Models;

use App\Models\Allocation as Allocation;
use App\Traits\Allocatable;
use App\Models\AllocationPercentage;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use Allocatable;

    protected $fillable = ['name','type'];
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
            1 => 'revenue',
            2 => 'pretotal',
            3 => 'salestax',
            4 => 'prereal',
            5 => 'postreal'
        ];
    }
/*
    public function allocations()
    {
        return $this->morphMany('App\Models\Allocation', 'allocatable');
    }
*/
    public function getAllocationPercentages($phase_id = null)
    {

        if($phase_id)
        {
            return AllocationPercentage::where('bank_account_id', '=', $this->id)->where('phase_id', '=', $phase_id)->get();
        }

        return AllocationPercentage::where('bank_account_id', '=', $this->id)->get();

    }

    public function getAllAllocationPercentages($phaseId)
    {
        return BankAccount::where('business_id', $this->business_id)
            ->with('percentages', function ($query) use ($phaseId) {
                return $query->where('phase_id', $phaseId);
            })
            ->get()
            ->mapToGroups(function ($item, $key) {
                return [$item->type => [
                    'id'=>$item->id,
                    'val' => count($item->percentages) ? $item->percentages[0]->percent : null]
                ];
            })->map(function($a_item){
                return array_column(collect($a_item)->toArray(), 'val', 'id');
            })->toArray();
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
        return AccountFlow::where('account_id', $this->id)
            ->with('allocations', function($query) use ($date, $phaseId) {
                return $query->where('allocation_date', $date)
                    ->where('phase_id', $phaseId);
            })
            ->get()
            ->map( function($item) {
                return collect($item->toArray())
                    ->only('negative_flow','allocations')
                    ->all();
            })
            ->map( function($a_item) {
                return count($a_item['allocations']) > 0
                    ? $a_item['negative_flow']
                        ? $a_item['allocations'][0]['amount'] * -1
                        : $a_item['allocations'][0]['amount']
                    : 0;
            })->sum();
    }

    /**
     * @param $businessId integer
     * @param $date       string
     * @return mixed
     */
    public function getRevenueByDate($businessId, $date)
    {
        return self::where('type', 'revenue')->where('business_id',$businessId)
            ->with('allocations', function($query) use ($date) {
                return $query->where('allocation_date', $date);
            })
            ->get()
            ->map( function($item) {
                return collect($item->toArray())
                    ->only('allocations')
                    ->all();
            })
            ->map( function($a_item) {
                return count($a_item['allocations']) > 0
                    ? $a_item['allocations'][0]['amount']
                    : 0;
            })->sum();
    }

    /**
     * @param $date     string
     * @param $phase_id integer
     * @return float|int
     */
    public function getTransferAmount($date, $phase_id)
    {
        $revenue = $this->getRevenueByDate($this->business_id, $date);
        $amount = 0;
        $percents = $this->getAllAllocationPercentages($phase_id);

        switch ($this->type)
        {
            case 'salestax':
                $amount = ($revenue > 0 )
                    ? round($revenue - $revenue / ($percents[$this->type][$this->id] / 100 + 1), 4)
                    : 0;
                break;

            case 'pretotal':
                $salestax = data_get($percents, 'salestax');
                $salestax = count($salestax) > 0 ? key($salestax) : null;
//                $ncr = ($revenue > 0 && is_numeric($salestax)) ? $revenue / ($salestax / 100 + 1) : 0;
                $ncr = 0;
                if (is_integer($salestax)) {
                    $allocation = Allocation::where('allocatable_id', $salestax)->where('allocation_date', $date)->first();
                    $ncr = $revenue > 0 && $allocation ? $revenue - $allocation->amount : 0;
                }
                $amount = ($ncr > 0 && is_numeric($percents[$this->type][$this->id]))
                    ? round($ncr - $ncr / ($percents[$this->type][$this->id] / 100 + 1), 4)
                    : 0;
        }

        return $amount;
    }

    /**
     * Return the tax rate for the account (if it has one)
     */
    public function taxRate()
    {
        return $this->hasOne(TaxRate::class);
    }


}
