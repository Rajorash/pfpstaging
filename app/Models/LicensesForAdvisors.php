<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensesForAdvisors extends Model
{
    use HasFactory;

    public const DEFAULT_LICENSES_COUNT = 5;

    protected $fillable = [
        'licenses',
    ];

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function regionalAdmin()
    {
        return $this->belongsTo(User::class, 'regional_admin_id');
    }
}
