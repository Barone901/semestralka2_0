<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use SoftDeletes;

    /**
     * Polia stránky, ktoré môžeš ukladať cez create/update.
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
     * Pretypovania.
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Povolené statusy stránky.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Statusy + text do UI (napr. select).
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
     * booted() = miesto, kde si vieš pridať model eventy (saving/creating/updating...).
     *
     * Tu riešime:
     * 1) generovanie slugu z title,
     * 2) a garantujeme unikátnosť slugu (ak už existuje, pridáme -1, -2, ...)
     */
    protected static function booted(): void
    {
        static::saving(function (self $page) {
            // Pri vytváraní: ak slug neexistuje, vygenerujeme z title.
            if (!$page->exists && empty($page->slug)) {
                $page->slug = Str::slug((string) $page->title);
            }

            // Pri update: ak sa zmenil title a slug si nezmenil ručne,
            // aktualizujeme slug podľa nového title.
            if ($page->exists && $page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = Str::slug((string) $page->title);
            }

            // Na záver vždy zabezpečíme, aby slug bol unikátny.
            if (!empty($page->slug)) {
                $page->slug = static::makeUniqueSlug($page, (string) $page->slug);
            }
        });
    }

    /**
     * Urobí slug unikátny:
     * ak existuje rovnaký slug, pridáme -1, -2, -3...
     */
    private static function makeUniqueSlug(self $page, string $slug): string
    {
        $base = $slug;
        $i = 1;

        // Query na kontrolu existencie rovnakého slugu.
        // Pri update sa musíme vyhnúť tomu, aby si stránka našla sama seba.
        $query = static::query()->where('slug', $slug);
        if ($page->exists) {
            $query->whereKeyNot($page->getKey());
        }

        // Kým slug existuje, skúšame ďalší variant.
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

    /**
     * Stránka patrí autorovi (user).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Stránka môže mať banner.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Scope: iba publikované stránky.
     * + ak published_at je v budúcnosti, ešte sa nezobrazí.
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
     * Scope: koncepty.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope: zvýraznené stránky.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: poradie pre výpis.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at');
    }

    /**
     * Virtuálny atribút: vráti URL obrázka.
     * - ak je to už http link, necháme ho
     * - inak berieme storage path
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
     * Pomocná metóda: je stránka publikovaná a "už nastal čas publikácie"?
     */
    public function isPublished(): bool
    {
        return (string) $this->status === self::STATUS_PUBLISHED
            && ($this->published_at === null || $this->published_at->lte(now()));
    }

    /**
     * Zvýši počítadlo zobrazení o 1.
     * (Použi napr. keď niekto otvorí stránku.)
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Virtuálny atribút: URL tejto stránky.
     */
    public function getUrlAttribute(): string
    {
        return route('pages.show', (string) $this->slug);
    }
}
