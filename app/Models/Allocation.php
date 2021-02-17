<?php

namespace App\Models;

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

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function accountFlow()
    {
        return $this->belongsTo(AccountFlow::class, 'allocatable_id', 'id');
    }

    public function business()
    {
        return $this->phase->business;
    }
}
