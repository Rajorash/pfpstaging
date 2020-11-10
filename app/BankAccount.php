<?php

namespace App;

use App\Traits\Allocatable;
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
        return $this->morphMany('App\Allocation', 'allocatable');
    }

    /**
     * Return the tax rate for the account (if it has one)
     */
    public function taxRate()
    {
        return $this->hasOne(TaxRate::class);
    }


}
