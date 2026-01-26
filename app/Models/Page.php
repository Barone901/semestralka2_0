<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Model stranky/clanku s podporou soft delete.
 * Relacie: User (N:1), Banner (N:1)
 */
class Page extends Model
{
    use SoftDeletes;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'author_id',
        'banner_id',
        'is_featured',
        'sort_order',
        'views_count',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'published_at' => 'datetime',
    ];

    // Konstanty pre statusy stranky
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Vrati dostupne statusy pre UI.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Koncept',
            self::STATUS_PUBLISHED => 'Publikované',
            self::STATUS_ARCHIVED => 'Archivované',
        ];
    }

    /**
     * Automaticke generovanie a unikatnost slugu pri ukladani.
     */
    protected static function booted(): void
    {
        static::saving(function (self $page) {
            if (!$page->exists && empty($page->slug)) {
                $page->slug = Str::slug((string) $page->title);
            }

            if ($page->exists && $page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = Str::slug((string) $page->title);
            }

            if (!empty($page->slug)) {
                $page->slug = static::makeUniqueSlug($page, (string) $page->slug);
            }
        });
    }

    /**
     * Vytvori unikatny slug pridanim ciselneho suffixu.
     */
    private static function makeUniqueSlug(self $page, string $slug): string
    {
        $base = $slug;
        $i = 1;

        $query = static::query()->where('slug', $slug);
        if ($page->exists) {
            $query->whereKeyNot($page->getKey());
        }

        while ($query->exists()) {
            $slug = "{$base}-{$i}";
            $i++;

            $query = static::query()->where('slug', $slug);
            if ($page->exists) {
                $query->whereKeyNot($page->getKey());
            }
        }

        return $slug;
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Stranka patri autorovi.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Stranka moze mat prideleny banner.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope: Iba publikovane stranky s platnym published_at.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope: Iba koncepty.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope: Iba zvyraznene stranky.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Zoradenie podla sort_order a published_at.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at');
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati URL hlavneho obrazku stranky.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        return str_starts_with((string) $this->featured_image, 'http')
            ? (string) $this->featured_image
            : asset('storage/' . $this->featured_image);
    }

    /**
     * Skontroluje ci je stranka publikovana a viditelna.
     */
    public function isPublished(): bool
    {
        return (string) $this->status === self::STATUS_PUBLISHED
            && ($this->published_at === null || $this->published_at->lte(now()));
    }

    /**
     * Zvysi pocitadlo zobrazeni o 1.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Vrati URL stranky.
     */
    public function getUrlAttribute(): string
    {
        return route('pages.show', (string) $this->slug);
    }
}
