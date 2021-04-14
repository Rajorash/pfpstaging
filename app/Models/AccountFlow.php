<?php

namespace App\Models;

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
        return $this->belongsTo(BankAccount::class, 'account_id', 'id');
    }

    public function isNegative()
    {
        return $this->negative_flow;
    }
}
