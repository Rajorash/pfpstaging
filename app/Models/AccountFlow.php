<?php

namespace App;

use App\Traits\Allocatable;
use Illuminate\Database\Eloquent\Model;

class AccountFlow extends Model
{
    use Allocatable;

    protected $fillable = ['name','negative_flow'];

    protected $casts = [
        'negative_flow' => 'boolean'
    ];

    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'id', 'account_id');
    }

    public function isNegative()
    {
        return $this->negative_flow;
    }

}
