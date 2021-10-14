<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BankAccountEntry
 *
 * @property int $id
 * @property int $bank_account_id
 * @property string $balance_date
 * @property string $balance_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount $account
 * @method static \Database\Factories\BankAccountEntryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry whereBalanceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry whereBalanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccountEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankAccountEntry extends Model
{
    use HasFactory;

    protected $fillable = ['date_entered', 'amount_entered', 'bank_account_id'];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}
