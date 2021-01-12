<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountEntry extends Model
{
    use HasFactory;

    protected $fillable = ['date_entered', 'amount_entered', 'bank_account_id'];

    public function account() {
        return $this->belongsTo(BankAccount::class);
    }

}
