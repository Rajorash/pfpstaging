<?php

namespace App\Models;

use Database\Factories\BankAccountEntryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BankAccountEntry
 *
 * @property int $id
 * @property int $bank_account_id
 * @property string $balance_date
 * @property string $balance_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BankAccount $account
 * @method static BankAccountEntryFactory factory(...$parameters)
 * @method static Builder|BankAccountEntry newModelQuery()
 * @method static Builder|BankAccountEntry newQuery()
 * @method static Builder|BankAccountEntry query()
 * @method static Builder|BankAccountEntry whereBalanceAmount($value)
 * @method static Builder|BankAccountEntry whereBalanceDate($value)
 * @method static Builder|BankAccountEntry whereBankAccountId($value)
 * @method static Builder|BankAccountEntry whereCreatedAt($value)
 * @method static Builder|BankAccountEntry whereId($value)
 * @method static Builder|BankAccountEntry whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BankAccountEntry extends Model
{
    use HasFactory;

    protected $fillable = ['date_entered', 'amount_entered', 'bank_account_id'];

    public function account()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
