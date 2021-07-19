<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAdvisor extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function advisors()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}
