<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * GET /api/products/search?q=...
     * Autocomplete / rýchle vyhľadávanie produktov (JSON).
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        $products = $this->productService
            ->search($q)
            ->map(fn ($product) => $this->productService->formatForApi($product))
            ->values();

        return response()->json($products);
    }

    /**
     * GET /api/products/{product}
     * Detail produktu pre AJAX modal (JSON).
     * Pozn.: route model binding ti natiahne Product automaticky.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json(
            $this->productService->formatDetailForApi($product)
        );
    }
}
