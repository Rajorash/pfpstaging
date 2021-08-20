<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\AllocationPercentage
 *
 * @property int $id
 * @property int $phase_id
 * @property int $bank_account_id
 * @property mixed $percent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|AllocationPercentage newModelQuery()
 * @method static Builder|AllocationPercentage newQuery()
 * @method static Builder|AllocationPercentage query()
 * @method static Builder|AllocationPercentage whereBankAccountId($value)
 * @method static Builder|AllocationPercentage whereCreatedAt($value)
 * @method static Builder|AllocationPercentage whereId($value)
 * @method static Builder|AllocationPercentage wherePercent($value)
 * @method static Builder|AllocationPercentage wherePhaseId($value)
 * @method static Builder|AllocationPercentage whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AllocationPercentage extends Model
{

    /**grep -iR getAllocationByDate ./app
     * ./app/Http/Livewire/Calculator/__AccountValue.php
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

        AllocationPercentage::saved(function ($model) {
            $phaseId = $model->phase_id ?? null;
            $businessId = optional($model->account())->business()->id ?? null;
            if ($phaseId && $businessId) {
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
