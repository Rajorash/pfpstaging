<?php

namespace App\Models;

use App\Traits\Allocatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\AccountFlow
 *
 * @property int $id
 * @property string $label
 * @property bool $negative_flow
 * @property int $account_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BankAccount $account
 * @property-read Collection|Allocation[] $allocations
 * @property-read int|null $allocations_count
 * @property-read Collection|RecurringTransactions[] $recurringTransactions
 * @property-read int|null $recurring_transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow whereNegativeFlow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountFlow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountFlow extends Model
{
    use Allocatable;

    protected $fillable = ['name', 'negative_flow'];

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

    public function recurringTransactions()
    {
        return $this->hasMany(RecurringTransactions::class, 'account_id', 'id');
    }
}
