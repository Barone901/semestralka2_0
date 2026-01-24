<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BannerService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Sem si injectuješ služby (Service layer).
     * Výhoda: controller ostáva tenký a logika je v službách.
     */
    public function __construct(
        private CategoryService $categoryService,
        private ProductService $productService,
        private BannerService $bannerService
    ) {}

    /**
     * Homepage:
     * - načíta hlavné kategórie
     * - načíta aktívne bannery
     * - načíta produkty (prípadne filtrované searchom)
     */
    public function index(Request $request): View
    {
        $categories = $this->categoryService->getParentCategories();
        $banners = $this->bannerService->getActiveBanners();

        // Search parameter (ak existuje), posielame ho do service
        $products = $this->productService->getProducts($request->get('search'));

        return view('pages.home', compact('categories', 'products', 'banners'));
    }
}
