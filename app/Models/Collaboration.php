<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collaboration extends Model
{
    protected $fillable = ['advisor_id', 'business_id'];

    public function advisor()
    {
        //return $this->belongsTo(Advisor::class);
        return $this->hasOne(Advisor::class, 'id', 'advisor_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function expiresAt()
    {
        if (is_null($this->expires_at)) {
            return false;
        }

        return $this->expires_at;
    }
}
