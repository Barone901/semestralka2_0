<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller pre pracu s nakupnym kosikom.
 */
class CartController extends Controller
{
    /**
     * Injektuje sluzbu pre kosik.
     */
    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Vrati aktualny obsah kosika ako JSON.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->cartService->getCart());
    }

    /**
     * Prida produkt do kosika s validaciou mnozstva a dostupnosti.
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

        if (!($result['success'] ?? false)) {
            return response()->json($result, 422);
        }

        return response()->json([
            ...$result,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    /**
     * Aktualizuje mnozstvo produktu v kosiku.
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
     * Odstrani produkt z kosika.
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
     * Vyprazdni cely kosik.
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
