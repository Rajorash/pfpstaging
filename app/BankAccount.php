<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['name','type'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
