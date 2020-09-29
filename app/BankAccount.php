<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['name','type'];
    protected $with = ['flows'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function flows()
    {
        return $this->hasMany(AccountFlow::class, 'account_id');
    }

    public static function type_list() {
        return [
            1 => 'revenue',
            2 => 'prereal',
            3 => 'postreal',
            4 => 'pretotal'
        ];
    }

}
