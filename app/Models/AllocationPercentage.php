<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationPercentage extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['phase_id', 'bank_account_id', 'percent'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['percent' => 'decimal:2'];

    public function phase() {
        $this->belongsTo(Phase::class);
    }

    public function account() {
        $this->belongsTo(BankAccount::class);
    }

}
