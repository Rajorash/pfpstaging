<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = ['phase_id', 'allocatable_id', 'allocatable_type', 'amount', 'allocation_date'];

    public function allocatable()
    {
        return $this->morphTo();
    }

    public function phase() {
        $this->belongsTo(Phase::class);
    }
}
