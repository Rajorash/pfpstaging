<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    protected $fillable = ['seats', 'niche', 'tier'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
