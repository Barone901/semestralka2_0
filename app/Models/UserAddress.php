<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAddressFormatting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model adresy pouzivatela.
 * Relacia: User (N:1)
 */
class UserAddress extends Model
{
    use HasAddressFormatting;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'user_id',
        'type',
        'is_default',
        'first_name',
        'last_name',
        'phone',
        'email',
        'street',
        'city',
        'postal_code',
        'country',
        'company_name',
        'ico',
        'dic',
        'ic_dph',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Konstanty pre typy adries
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_BILLING  = 'billing';

    public const DEFAULT_COUNTRY = 'Slovensko';

    /**
     * Adresa patri pouzivatelovi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Iba dorucovacie adresy.
     */
    public function scopeShipping(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SHIPPING);
    }

    /**
     * Scope: Iba fakturacne adresy.
     */
    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BILLING);
    }

    /**
     * Scope: Iba predvolene adresy.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
