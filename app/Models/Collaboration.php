<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collaboration extends Model
{
    public function advisor()
    {
        return $this->belongsTo(Advisor::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

}
