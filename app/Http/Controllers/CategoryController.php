<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\BannerService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre pracu s kategoriami produktov.
 */
class CategoryController extends Controller
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
     * Zobrazi detail kategorie s produktmi a podporou vyhladavania.
     */
    public function show(Category $category, Request $request): View
    {
        // Server-side validácia search query
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $categories = $this->categoryService->getParentCategories();
        $banners = $this->bannerService->getActiveBanners();

        // Získame ID kategórie + jej detí (aby produkty zahŕňali aj podkategórie)
        $categoryIds = $this->categoryService->getCategoryWithChildrenIds($category);

        // Vyhľadávanie posielame do ProductService
        $products = $this->productService->getProducts(
            $validated['search'] ?? null,
            $categoryIds
        );

        // Používaš rovnaký view ako homepage – to je OK, ak je layout jednotný.
        return view('pages.home', compact('categories', 'products', 'category', 'banners'));
    }
}
