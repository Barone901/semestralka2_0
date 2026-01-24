<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'guest_token',
        'guest_email',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_cost',
        'total',
        'note',
    ];

    protected $hidden = [
        'guest_token',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING => 'Pending',
        self::PAYMENT_PAID => 'Paid',
        self::PAYMENT_FAILED => 'Failed',
    ];

    public const PAYMENT_COD = 'cod';

    public const PAYMENT_METHODS = [
        self::PAYMENT_COD => 'Cash on Delivery',
    ];

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid('', true), -4));

        return "{$prefix}-{$date}-{$random}";
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', OrderAddress::TYPE_SHIPPING);
    }

    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', OrderAddress::TYPE_BILLING);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format((float) $this->total, 2);
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return '€' . number_format((float) $this->shipping_cost, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '€' . number_format((float) $this->subtotal, 2);
    }

    public function getStatusTextAttribute(): string
    {
        return self::STATUSES[$this->status] ?? (string) $this->status;
    }

    public function getPaymentStatusTextAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? (string) $this->payment_status;
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? (string) $this->payment_method;
    }

    public function getStatusColorAttribute(): string
    {
        return match ((string) $this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PROCESSING => 'indigo',
            self::STATUS_SHIPPED => 'purple',
            self::STATUS_DELIVERED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }
}
