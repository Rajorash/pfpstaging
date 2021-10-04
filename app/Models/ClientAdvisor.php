<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientAdvisor
 *
 * @property int $client_id
 * @property int $advisor_id
 * @property-read User $advisors
 * @property-read User $client
 * @method static Builder|ClientAdvisor newModelQuery()
 * @method static Builder|ClientAdvisor newQuery()
 * @method static Builder|ClientAdvisor query()
 * @method static Builder|ClientAdvisor whereAdvisorId($value)
 * @method static Builder|ClientAdvisor whereClientId($value)
 * @mixin Eloquent
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
