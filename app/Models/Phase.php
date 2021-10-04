<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Phase
 *
 * @property int $id
 * @property int $business_id
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Business $business
 * @property-read Collection|\App\Models\AllocationPercentage[] $percentages
 * @property-read int|null $percentages_count
 * @method static Builder|Phase newModelQuery()
 * @method static Builder|Phase newQuery()
 * @method static Builder|Phase query()
 * @method static Builder|Phase whereBusinessId($value)
 * @method static Builder|Phase whereCreatedAt($value)
 * @method static Builder|Phase whereEndDate($value)
 * @method static Builder|Phase whereId($value)
 * @method static Builder|Phase whereStartDate($value)
 * @method static Builder|Phase whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|\App\Models\Allocation[] $allocations
 * @property-read int|null $allocations_count
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
