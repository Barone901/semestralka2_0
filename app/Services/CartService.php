<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Sluzba pre pracu s nakupnym kosikom.
 */
class CartService
{
    /**
     * Ziska obsah kosika vratane vypocitanych hodnot.
     */
    public function getCart(): array
    {
        $cart = session()->get('cart', []);
        $items = collect($cart)->map(function ($item) {
            $imageUrl = $item['image_url'] ?? null;
            if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = asset('storage/' . $imageUrl);
            }

            $subtotal = $item['price'] * $item['quantity'];

            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'price' => $item['price'],
                'formatted_price' => $this->formatPrice($item['price']),
                'quantity' => $item['quantity'],
                'image_url' => $imageUrl,
                'subtotal' => $subtotal,
                'formatted_subtotal' => $this->formatPrice($subtotal),
                'url' => route('product.show', $item['slug']),
            ];
        })->values();

        $total = $items->sum('subtotal');

        return [
            'items' => $items,
            'total' => $total,
            'formatted_total' => $this->formatPrice($total),
            'count' => $items->sum('quantity'),
        ];
    }

    /**
     * Prida produkt do kosika s kontrolou skladu.
     */
    public function addProduct(Product $product, int $quantity = 1): array
    {
        if ($product->stock < $quantity) {
            return [
                'success' => false,
                'message' => 'Not enough items in stock.',
            ];
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'quantity' => $quantity,
                'image_url' => $product->image_url,
            ];
        }

        session()->put('cart', $cart);

        return [
            'success' => true,
            'message' => 'Product has been added to cart.',
        ];
    }

    /**
     * Aktualizuje mnozstvo produktu v kosiku.
     */
    public function updateQuantity(int $productId, int $quantity): array
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            return [
                'success' => false,
                'message' => 'Product is not in cart.',
            ];
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::find($productId);
            if ($product && $product->stock < $quantity) {
                return [
                    'success' => false,
                    'message' => 'Not enough items in stock.',
                ];
            }
            $cart[$productId]['quantity'] = $quantity;
        }

        session()->put('cart', $cart);

        return [
            'success' => true,
            'message' => 'Cart has been updated.',
        ];
    }

    /**
     * Odstrani produkt z kosika.
     */
    public function removeProduct(int $productId): array
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return [
            'success' => true,
            'message' => 'Product has been removed from cart.',
        ];
    }

    /**
     * Vyprazdni cely kosik.
     */
    public function clear(): array
    {
        session()->forget('cart');

        return [
            'success' => true,
            'message' => 'Cart has been cleared.',
        ];
    }

    /**
     * Formatuje cenu s menovym symbolom.
     */
    private function formatPrice(float $price): string
    {
        return '€' . number_format($price, 2);
    }
}
