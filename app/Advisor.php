<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    const DEFAULT_SEATS = 5;

    protected $fillable = ['niche', 'tier'];

    public function __construct($user_id, $seats = DEFAULT_SEATS, $niche = null, $tier = null)
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
