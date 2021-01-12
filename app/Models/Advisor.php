<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    protected $fillable = ['niche', 'tier'];

    public function __construct($user_id, $seats = 5, $niche = null, $tier = null)
    {
        $this->id = $user_id;
        $this->seat_limit = $seats;
        $this->niche = $niche;
        $this->tier = $tier;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
