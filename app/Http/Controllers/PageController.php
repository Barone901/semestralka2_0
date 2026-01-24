<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private PageService $pageService
    ) {}

    /**
     * Zoznam publikovaných článkov/stránok.
     * - service typicky filtruje status "published" + published_at <= now
     */
    public function index(): View
    {
        $pages = $this->pageService->getPublishedPages();

        return view('pages.articles.index', compact('pages'));
    }

    /**
     * Detail článku podľa slug-u.
     * - ak slug neexistuje → 404
     * - zvýšime views_count
     * - načítame related pages (podľa tvojej logiky v service)
     */
    public function show(string $slug): View
    {
        $page = $this->pageService->findBySlug($slug);

        if (!$page) {
            abort(404);
        }

        // Inkrementuj počet zobrazení
        $page->incrementViews();

        $relatedPages = $this->pageService->getRelatedPages($page);

        return view('pages.articles.show', compact('page', 'relatedPages'));
    }
}
