<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function allowTo($permission)
    {
        $this->permissions()->sync($permission, false);
    }
}
