<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PageService
{
    /**
     * Získa stránkovaný zoznam publikovaných stránok.
     */
    public function getPublishedPages(int $perPage = 12): LengthAwarePaginator
    {
        return Page::published()
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Nájde publikovanú stránku podľa slug.
     */
    public function findBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)
            ->published()
            ->first();
    }

    /**
     * Získa súvisiace stránky.
     */
    public function getRelatedPages(Page $page, int $limit = 3): Collection
    {
        $relatedPages = Page::published()
            ->where('id', '!=', $page->id)
            ->featured()
            ->limit($limit)
            ->get();

        if ($relatedPages->count() < $limit) {
            $additionalPages = Page::published()
                ->where('id', '!=', $page->id)
                ->whereNotIn('id', $relatedPages->pluck('id'))
                ->latest('published_at')
                ->limit($limit - $relatedPages->count())
                ->get();

            $relatedPages = $relatedPages->merge($additionalPages);
        }

        return $relatedPages;
    }
}

