<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    public function allocatable()
    {
        return $this->morphTo();
    }
}
