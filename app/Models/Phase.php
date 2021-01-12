<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    public const DEFAULT_PHASE_COUNT = 8;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['end_date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['end_date' => 'date'];

    public function business() {
        return $this->belongsTo(Business::class);
    }

    public function allocations() {
        return $this->hasMany(Allocations::class);
    }

    public function percentages() {
        return $this->hasMany(AllocationPercentage::class);
    }

}
