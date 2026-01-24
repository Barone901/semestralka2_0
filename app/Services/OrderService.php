<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function createOrder(array $orderData): array
    {
        $cart = $this->cartService->getCart();

        if (($cart['count'] ?? 0) === 0) {
            return [
                'success' => false,
                'message' => 'Your cart is empty.',
            ];
        }

        try {
            $order = DB::transaction(function () use ($orderData, $cart) {
                $shippingCost = 3.00;
                $subtotal = (float) ($cart['total'] ?? 0);
                $total = $subtotal + $shippingCost;

                $guestToken = Auth::check() ? null : Str::random(64);

                // 1) Create order
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => Auth::id(),
                    'guest_token' => $guestToken,
                    'guest_email' => Auth::check() ? null : ($orderData['shipping_email'] ?? null),

                    'status' => Order::STATUS_PENDING,
                    'payment_method' => Order::PAYMENT_COD,
                    'payment_status' => Order::PAYMENT_PENDING,

                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,

                    'note' => $orderData['note'] ?? null,
                ]);

                // 2) Create / update address snapshots
                $order->addresses()->updateOrCreate(
                    ['type' => OrderAddress::TYPE_SHIPPING],
                    OrderAddress::attributesFromCheckout($orderData, OrderAddress::TYPE_SHIPPING)
                );

                $order->addresses()->updateOrCreate(
                    ['type' => OrderAddress::TYPE_BILLING],
                    OrderAddress::attributesFromCheckout($orderData, OrderAddress::TYPE_BILLING)
                );

                // 3) Guest token do session (mimo DB, ale nechávam to tu — je to OK)
                if ($guestToken) {
                    session(['guest_order_token' => $guestToken]);
                }

                // 4) Create items + safe stock decrement (lockForUpdate)
                foreach (($cart['items'] ?? []) as $cartItem) {
                    $productId = (int) ($cartItem['id'] ?? 0);
                    $qty = (int) ($cartItem['quantity'] ?? 0);

                    if ($productId <= 0 || $qty <= 0) {
                        throw new \RuntimeException('Invalid cart item.');
                    }

                    /** @var Product|null $product */
                    $product = Product::query()
                        ->whereKey($productId)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw new \RuntimeException("Product (ID {$productId}) not found.");
                    }

                    // Ak máš soft-deletes alebo active flag, tu je miesto to skontrolovať
                    // if ($product->is_active === false) { ... }

                    $currentStock = (int) $product->stock;

                    if ($currentStock < $qty) {
                        // Správa pre usera (môžeš si ju zjemniť)
                        throw new \RuntimeException("Not enough stock for \"{$product->name}\". Available: {$currentStock}.");
                    }

                    // Snapshot dát do order item (použi reálne hodnoty z DB, nie z košíka)
                    $unitPrice = (float) $product->price;
                    $lineSubtotal = $unitPrice * $qty;

                    // Ukladanie cez relationship: $order->items()->create(...)
                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $unitPrice,
                        'quantity' => $qty,
                        'subtotal' => $lineSubtotal,
                    ]);

                    // Bezpečné odrátanie skladu
                    $product->decrement('stock', $qty);
                }

                // 5) Clear cart až po úspechu
                $this->cartService->clear();

                return $order->load(['shippingAddress', 'billingAddress', 'items']);
            });

            return [
                'success' => true,
                'message' => 'Order has been successfully created.',
                'order' => $order,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while creating the order: ' . $e->getMessage(),
            ];
        }
    }

    public function getUserOrders(?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return Order::where('user_id', $userId)
            ->with(['items', 'shippingAddress', 'billingAddress'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getOrder(int $orderId, ?int $userId = null): ?Order
    {
        $query = Order::with(['items.product', 'shippingAddress', 'billingAddress']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->find($orderId);
    }

    public function getOrderByNumber(string $orderNumber, ?int $userId = null): ?Order
    {
        $query = Order::with(['items.product', 'shippingAddress', 'billingAddress'])
            ->where('order_number', $orderNumber);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->first();
    }

    public function cancelOrder(Order $order): array
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            return [
                'success' => false,
                'message' => 'Order is already cancelled.',
            ];
        }

        if (in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)) {
            return [
                'success' => false,
                'message' => 'Cannot cancel shipped or delivered orders.',
            ];
        }

        try {
            DB::transaction(function () use ($order) {
                // Lockni produkty, aby sa ti stock neprebil s inými operáciami
                foreach ($order->items as $item) {
                    if (!$item->product_id) {
                        continue;
                    }

                    $product = Product::query()
                        ->whereKey($item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($product) {
                        $product->increment('stock', (int) $item->quantity);
                    }
                }

                $order->update(['status' => Order::STATUS_CANCELLED]);
            });

            return [
                'success' => true,
                'message' => 'Order has been cancelled.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage(),
            ];
        }
    }

    public function updateStatus(Order $order, string $status): array
    {
        if (!array_key_exists($status, Order::STATUSES)) {
            return [
                'success' => false,
                'message' => 'Invalid status.',
            ];
        }

        $order->update(['status' => $status]);

        return [
            'success' => true,
            'message' => 'Order status updated.',
        ];
    }
}
