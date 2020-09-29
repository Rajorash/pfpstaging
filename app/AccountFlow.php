<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountFlow extends Model
{
    protected $fillable = ['name','negative_flow'];

    protected $casts = [
        'negative_flow' => 'boolean'
    ];

    public function account()
    {
        return $this->belongsTo(BankAccount::class);
    }

}
