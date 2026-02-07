<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Sluzba pre pracu s produktami.
 */
class ProductService
{
    /**
     * Vyhlada produkty podla zadaneho vyrazu.
     */
    public function search(string $query, int $limit = 10): Collection
    {
        if (strlen($query) < 2) {
            return collect([]);
        }

        return Product::search($query)
            ->with('category')
            ->limit($limit)
            ->get();
    }

    /**
     * Ziska produkty s filtrovanim a strankovanim.
     */
    public function getProducts(?string $search = null, ?array $categoryIds = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with('category');

        if ($categoryIds) {
            $query->whereIn('category_id', $categoryIds);
        }

        if ($search) {
            $query->search($search);
        }

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Ziska suvisiace produkty z rovnakej kategorie.
     */
    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inStock()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Formatuje produkt pre API odpoved.
     */
    public function formatForApi(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'image_url' => $this->getImageUrl($product),
            'category' => $product->category?->name,
            'in_stock' => $product->is_in_stock,
            'url' => route('product.show', $product),
        ];
    }

    /**
     * Formatuje produkt s detailmi pre API odpoved.
     */
    public function formatDetailForApi(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'stock' => $product->stock,
            'in_stock' => $product->is_in_stock,
            'image_url' => $this->getImageUrl($product),
            'category' => [
                'id' => $product->category?->id,
                'name' => $product->category?->name,
                'slug' => $product->category?->slug,
            ],
            'url' => route('product.show', $product),
        ];
    }

    /**
     * Ziska URL obrazku produktu.
     */
    private function getImageUrl(Product $product): ?string
    {
        if (!$product->image_url) {
            return null;
        }

        return str_starts_with($product->image_url, 'http')
            ? $product->image_url
            : asset('storage/' . $product->image_url);
    }
}
