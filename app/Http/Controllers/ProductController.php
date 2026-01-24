<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $query = Product::query();

        // Filter podľa kategórie (slug)
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', (string) $request->category);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = (string) $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = (string) $request->get('sort', 'newest');

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

    public function show(Product $product): View
    {
        $product->load('category');
        $related = $this->productService->getRelatedProducts($product);

        return view('pages.products.show', compact('product', 'related'));
    }
}
