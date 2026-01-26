<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BannerService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre hlavnu stranku eshopu.
 */
class HomeController extends Controller
{
    /**
     * Injektuje sluzby pre kategorie, produkty a bannery.
     */
    public function __construct(
        private CategoryService $categoryService,
        private ProductService $productService,
        private BannerService $bannerService
    ) {}

    /**
     * Zobrazi hlavnu stranku s produktmi, kategoriami a bannermi.
     */
    public function index(Request $request): View
    {
        // Server-side validacia search query
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $categories = $this->categoryService->getParentCategories();
        $banners = $this->bannerService->getActiveBanners();

        $products = $this->productService->getProducts($validated['search'] ?? null);

        return view('pages.home', compact('categories', 'products', 'banners'));
    }
}
