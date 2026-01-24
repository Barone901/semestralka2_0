<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Category Model
 *
 * Represents a product category with support for nested hierarchy.
 *
 * Relations:
 * - Category (N:1) - Category belongs to a parent category (self-referential)
 * - Category (1:N) - Category has many child categories (self-referential)
 * - Product (1:N) - Category has many products
 */
class Category extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image_url',
        'sort_order',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
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
     * Category belongs to a parent category.
     * Relation: N:1 (Many categories can have one parent - self-referential)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Category has many child categories.
     * Relation: 1:N (One category has many children - self-referential)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Category has many products.
     * Relation: 1:N (One category has many products)
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->latest();
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Only parent categories (no parent_id).
     */
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Order by sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Check if category has child categories.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if this is a parent category (no parent_id).
     */
    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Get all category IDs including this one and all descendants (recursive).
     * Useful for filtering products in category and subcategories.
     *
     * @return list<int>
     */
    public function getAllCategoryIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = [...$ids, ...$child->getAllCategoryIds()];
        }

        return $ids;
    }
}
