<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaxRate
 *
 * @property int $id
 * @property string $rate
 * @property int $bank_account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount $account
 * @method static \Database\Factories\TaxRateFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereBankAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereUpdatedAt($value)
 * @mixin \Eloquent
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
