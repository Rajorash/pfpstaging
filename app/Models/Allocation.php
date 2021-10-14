<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Allocation
 *
 * @property int $id
 * @property int $phase_id
 * @property int $allocatable_id
 * @property string $allocatable_type
 * @property string $amount
 * @property mixed $allocation_date
 * @property int|null $manual_entry
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $allocatable
 * @property-read \App\Models\Phase $phase
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereAllocatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereAllocatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereAllocationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereManualEntry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation wherePhaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Allocation extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['allocation_date' => 'date:Y-m-j'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phase_id',
        'allocatable_id',
        'allocatable_type',
        'amount',
        'allocation_date',
        'manual_entry'
    ];

    public function allocatable()
    {
        return $this->morphTo();
    }

    public function phase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function business()
    {
        return $this->phase->business;
    }
}
