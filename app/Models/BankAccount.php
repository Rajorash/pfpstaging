<?php

namespace App\Models;

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

    public function allocations()
    {
        return $this->morphMany('App\Models\Allocation', 'allocatable');
    }

    public function getAllocationPercentages($phase_id = null)
    {

        if($phase_id)
        {
            return AllocationPercentage::where('bank_account_id', '=', $this->id)->where('phase_id', '=', $phase_id)->get();
        }

        return AllocationPercentage::where('bank_account_id', '=', $this->id)->get();

    }

    /**
     * Return the tax rate for the account (if it has one)
     */
    public function taxRate()
    {
        return $this->hasOne(TaxRate::class);
    }


}
