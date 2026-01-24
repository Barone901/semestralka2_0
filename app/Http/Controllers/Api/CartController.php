<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * GET /api/cart
     * Vráti aktuálny košík ako JSON (items, count, total, …).
     */
    public function index(): JsonResponse
    {
        return response()->json($this->cartService->getCart());
    }

    /**
     * POST /api/cart/add
     * Pridá produkt do košíka.
     * Očakáva: product_id, quantity (voliteľné)
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product  = Product::findOrFail($validated['product_id']);
        $quantity = (int) ($validated['quantity'] ?? 1);

        $result = $this->cartService->addProduct($product, $quantity);

        // Ak služba vráti error (napr. nedostatok skladom), pošleme 422.
        if (!($result['success'] ?? false)) {
            return response()->json($result, 422);
        }

        return response()->json([
            ...$result,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    /**
     * POST /api/cart/update
     * Zmení množstvo položky v košíku.
     * quantity = 0 znamená “zmaž položku” (ak to tak máš v CartService).
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $result = $this->cartService->updateQuantity(
            (int) $validated['product_id'],
            (int) $validated['quantity']
        );

        // Keď položka neexistuje → 404, inak validačný problém → 422.
        if (!($result['success'] ?? false)) {
            $status = str_contains((string) ($result['message'] ?? ''), 'nie je') ? 404 : 422;
            return response()->json($result, $status);
        }

        return response()->json([
            ...$result,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    /**
     * POST /api/cart/remove
     * Odstráni produkt z košíka.
     */
    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $result = $this->cartService->removeProduct((int) $validated['product_id']);

        return response()->json([
            ...$result,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    /**
     * POST /api/cart/clear
     * Vyprázdni celý košík.
     */
    public function clear(): JsonResponse
    {
        $result = $this->cartService->clear();

        return response()->json([
            ...$result,
            'cart' => $this->cartService->getCart(),
        ]);
    }
}
