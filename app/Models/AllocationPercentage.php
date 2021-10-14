<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\AllocationPercentage
 *
 * @property int $id
 * @property int $phase_id
 * @property int $bank_account_id
 * @property mixed $percent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage query()
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage wherePhaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllocationPercentage whereUpdatedAt($value)
 * @mixin \Eloquent
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

                if (\Config::get('app.pfp_cache')) {
                    Cache::forget($key);
                };
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
