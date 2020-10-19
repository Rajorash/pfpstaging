<?php

namespace App\Traits;

trait Allocatable
{
    public function allocations()
    {
        return $this->morphMany('App\Allocation', 'allocatable');
    }
}
