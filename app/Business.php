<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function collaboration()
    {
        return $this->hasOne(Collaboration::class);
    }

    public function license()
    {
        return $this->hasOne(License::class);
    }

    public function accounts()
    {
        return $this->hasMany(BankAccount::class);
    }
 
}
