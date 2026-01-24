<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banner extends Model
{
    /**
     * Povolené polia na ukladanie bannera.
     */
    protected $fillable = [
        'name',
        'image_path',
        'link_url',
        'page_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Banner môže ukazovať na konkrétnu stránku (page_id).
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Ak máš v pages stĺpec banner_id, tak banner "má veľa" stránok.
     * (Takto vieš zistiť, ktoré stránky používajú tento banner.)
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Virtuálny atribút: finálny odkaz bannera.
     * Priorita:
     * 1) ak je vyplnený link_url -> použije sa
     * 2) inak, ak je page_id -> vygeneruje sa route na stránku
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
     * Scope: iba aktívne bannery.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: zoradenie bannerov podľa sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Virtuálny atribút: vráti URL obrázka.
     * - ak je to http, necháme
     * - inak storage/
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
