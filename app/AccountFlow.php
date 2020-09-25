<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountFlow extends Model
{
    protected $fillable = ['name','negative_flow'];

    public function account()
    {
        return $this->belongsTo(BankAccount::class);
    }

}
