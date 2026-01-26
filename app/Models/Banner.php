<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model bannera pre homepage slider.
 * Relacie: Page (N:1), Page (1:N)
 */
class Banner extends Model
{
    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'name',
        'image_path',
        'link_url',
        'page_id',
        'is_active',
        'sort_order',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Banner moze ukazovat na konkretnu stranku.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Banner ma viacero stranok ktore ho pouzivaju.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Vrati finalny odkaz bannera (link_url alebo route na stranku).
     */
    public function getLinkAttribute(): ?string
    {
        if (!empty($this->link_url)) {
            return (string) $this->link_url;
        }

        if (!empty($this->page_id) && $this->page) {
            return route('pages.show', (string) $this->page->slug);
        }

        return null;
    }

    /**
     * Scope: Iba aktivne bannery.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Zoradenie podla sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Vrati URL obrazku bannera.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        return str_starts_with((string) $this->image_path, 'http')
            ? (string) $this->image_path
            : asset('storage/' . $this->image_path);
    }
}
