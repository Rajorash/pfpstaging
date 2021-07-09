<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisorAdmin extends Model
{
    use HasFactory;

    public function advisors()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function regionalAdmin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
