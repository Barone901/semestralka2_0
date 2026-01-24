<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\BannerService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Inject služieb:
     * - CategoryService: veci okolo kategórií (parents, children ids, ...)
     * - ProductService: filtrovanie produktov
     * - BannerService: bannery do layoutu
     */
    public function __construct(
        private CategoryService $categoryService,
        private ProductService $productService,
        private BannerService $bannerService
    ) {}

    /**
     * Detail kategórie:
     * - zobrazí produkty v danej kategórii + jej podkategóriách
     * - podporuje vyhľadávanie (search)
     *
     * Pozn.: Category $category je route model binding.
     */
    public function show(Category $category, Request $request): View
    {
        $categories = $this->categoryService->getParentCategories();
        $banners = $this->bannerService->getActiveBanners();

        // Získame ID kategórie + jej detí (aby produkty zahŕňali aj podkategórie)
        $categoryIds = $this->categoryService->getCategoryWithChildrenIds($category);

        // Vyhľadávanie posielame do ProductService
        $products = $this->productService->getProducts(
            $request->get('search'),
            $categoryIds
        );

        // Používaš rovnaký view ako homepage – to je OK, ak je layout jednotný.
        return view('pages.home', compact('categories', 'products', 'category', 'banners'));
    }
}
