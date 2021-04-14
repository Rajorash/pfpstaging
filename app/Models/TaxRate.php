<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['rate'];

    /**
     * Return the bank account for the tax rate
     */
    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

}
