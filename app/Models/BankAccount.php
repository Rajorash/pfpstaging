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

    /**
     * Return the sum of allocations for the given date and current account
     *
     * @param $date
     * @return mixed
     */
    public function getAllocationsTotalByDate($date)
    {
        return AccountFlow::where('account_id', $this->id)
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
                return collect($a_item['allocations'])->toArray();
            })
            ->map( function($f_item) {
                return count($f_item)>0 ? collect($f_item[0])->only('amount') : 0;
            })
            ->sum('amount');
    }

    /**
     * Return the tax rate for the account (if it has one)
     */
    public function taxRate()
    {
        return $this->hasOne(TaxRate::class);
    }


}
