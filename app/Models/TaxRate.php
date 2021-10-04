<?php

namespace App\Models;

use Database\Factories\TaxRateFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\TaxRate
 *
 * @property int $id
 * @property string $rate
 * @property int $bank_account_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BankAccount $account
 * @method static TaxRateFactory factory(...$parameters)
 * @method static Builder|TaxRate newModelQuery()
 * @method static Builder|TaxRate newQuery()
 * @method static Builder|TaxRate query()
 * @method static Builder|TaxRate whereBankAccountId($value)
 * @method static Builder|TaxRate whereCreatedAt($value)
 * @method static Builder|TaxRate whereId($value)
 * @method static Builder|TaxRate whereRate($value)
 * @method static Builder|TaxRate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TaxRate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['rate'];

    /**
     * Return the bank account for the tax rate
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

}
