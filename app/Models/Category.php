<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model kategorie produktov s podporou hierarchie.
 * Relacie: Category (N:1 self), Category (1:N self), Product (1:N)
 */
class Category extends Model
{
    /**
     * Polia povolene pre hromadne priradenie.
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
     * Pretypovanie atributov.
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Route model binding pouziva slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Kategoria patri do rodricovskej kategorie.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Kategoria ma viacero detskych kategorii.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Kategoria ma viacero produktov.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->latest();
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Iba hlavne kategorie (bez parent_id).
     */
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Zoradenie podla sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    // =========================================================================
    // POMOCNE METODY
    // =========================================================================

    /**
     * Skontroluje ci ma kategoria detske kategorie.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Skontroluje ci je to hlavna kategoria.
     */
    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Vrati vsetky ID kategorie vratane vsetkych potomkov rekurzivne.
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
