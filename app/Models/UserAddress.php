<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAddressFormatting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasAddressFormatting;

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

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_BILLING  = 'billing';

    public const DEFAULT_COUNTRY = 'Slovensko';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeShipping(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SHIPPING);
    }

    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BILLING);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }
}
