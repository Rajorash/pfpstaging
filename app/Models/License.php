<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = ['account_number', 'business_id', 'advisor_id', 'active'];

    public function advisor()
    {
        return $this->hasOne(User::class, 'id', 'advisor_id');
    }

    public function business()
    {
        return $this->hasOne(Business::class);
    }
}
