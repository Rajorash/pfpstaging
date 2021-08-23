<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $allocatable
 * @property-read Phase $phase
 * @method static Builder|Allocation newModelQuery()
 * @method static Builder|Allocation newQuery()
 * @method static Builder|Allocation query()
 * @method static Builder|Allocation whereAllocatableId($value)
 * @method static Builder|Allocation whereAllocatableType($value)
 * @method static Builder|Allocation whereAllocationDate($value)
 * @method static Builder|Allocation whereAmount($value)
 * @method static Builder|Allocation whereCreatedAt($value)
 * @method static Builder|Allocation whereId($value)
 * @method static Builder|Allocation whereManualEntry($value)
 * @method static Builder|Allocation wherePhaseId($value)
 * @method static Builder|Allocation whereUpdatedAt($value)
 * @mixin Eloquent
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

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function business()
    {
        return $this->phase->business;
    }
}
