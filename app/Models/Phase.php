<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Phase
 *
 * @property int $id
 * @property int $business_id
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Allocation[] $allocations
 * @property-read int|null $allocations_count
 * @property-read \App\Models\Business $business
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AllocationPercentage[] $percentages
 * @property-read int|null $percentages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Phase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Phase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Phase query()
 * @method static \Illuminate\Database\Eloquent\Builder|Phase whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phase whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phase whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Phase extends Model
{
    public const DEFAULT_PHASE_COUNT = 8;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['end_date', 'start_date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['end_date' => 'date', 'start_date' => 'date'];

    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function allocations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function percentages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AllocationPercentage::class);
    }

}
