<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientAdvisor
 *
 * @property int $client_id
 * @property int $advisor_id
 * @property-read \App\Models\User $advisors
 * @property-read \App\Models\User $client
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAdvisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAdvisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAdvisor query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAdvisor whereAdvisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAdvisor whereClientId($value)
 * @mixin \Eloquent
 */
class ClientAdvisor extends Model
{
    use HasFactory;

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function advisors(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}
