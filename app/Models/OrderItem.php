<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderItem Model
 *
 * Represents a single item in an order. Stores a snapshot of product data
 * at the time of purchase to preserve order integrity.
 *
 * Relations:
 * - Order (N:1) - Many items belong to one order
 * - Product (N:1) - Item references a product (for linking, not data)
 */
class OrderItem extends Model
{
    /**
     * Mass assignable attributes.
     * Note: product_name, price stored as snapshot (not from relation).
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Item belongs to an order.
     * Relation: N:1 (Many items belong to one order)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Item references a product (for navigation/display).
     * Relation: N:1 (Many items can reference one product)
     * Note: The actual product data is stored in this item as snapshot.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Formatted unit price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '€' . number_format((float) $this->price, 2);
    }

    /**
     * Formatted line subtotal (price * quantity).
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return '€' . number_format((float) $this->subtotal, 2);
    }
}
