<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllocationPercentage extends Model
{

    /**grep -iR getAllocationByDate ./app
./app/Http/Livewire/Calculator/__AccountValue.php
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

    public static function boot()
    {
        parent::boot();

        AllocationPercentage::saved(function($model)
        {
            $phaseId = $model->phase_id ?? null;
            $businessId = optional($model->account())->business()->id ?? null;
            if ($phaseId && $businessId)
            {
                $key = 'phasePercentValues_'.$phaseId.'_'.$businessId;

                Cache::forget($key);
            }
        });
    }

    public function phase()
    {
        $this->belongsTo(Phase::class);
    }

    public function account()
    {
        $this->belongsTo(BankAccount::class);
    }



}
