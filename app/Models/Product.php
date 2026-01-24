<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Product Model
 *
 * Represents a product in the shop.
 *
 * Relations:
 * - Category (N:1) - Product belongs to one category
 * - OrderItem (1:N) - Product has many order items (historical references)
 */
class Product extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'category_id',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Route model binding uses slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Product belongs to a category.
     * Relation: N:1 (Many products belong to one category)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Product has many order items (historical references).
     * Relation: 1:N (One product referenced in many order items)
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Only products in stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope: Search by name or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '€' . number_format((float) $this->price, 2);
    }

    /**
     * Check if product is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return (int) $this->stock > 0;
    }
}
