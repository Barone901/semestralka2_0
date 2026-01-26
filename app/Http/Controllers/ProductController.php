<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre zobrazenie a filtrovanie produktov.
 */
class ProductController extends Controller
{
    /**
     * Injektuje sluzbu pre produkty.
     */
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Zobrazi zoznam produktov s filtrovanim, vyhladavanim a radenim.
     */
    public function index(Request $request): View
    {
        // Server-side validácia všetkých vstupov
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:newest,price_asc,price_desc,name'],
        ]);

        $query = Product::query();

        // Filter podľa kategórie (slug)
        if (!empty($validated['category'])) {
            $query->whereHas('category', function ($q) use ($validated) {
                $q->where('slug', $validated['category']);
            });
        }

        // Search
        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $validated['sort'] ?? 'newest';

        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->with('category')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('pages.products.index', compact('products', 'categories'));
    }

    /**
     * Zobrazi detail produktu s kategoriou a suvisiace produkty.
     */
    public function show(Product $product): View
    {
        $product->load('category');
        $related = $this->productService->getRelatedProducts($product);

        return view('pages.products.show', compact('product', 'related'));
    }
}
