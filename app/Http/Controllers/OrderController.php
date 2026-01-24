<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService
    ) {}

    public function checkout(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $shippingCost = 3.00; // COD fixed price

        $defaultShippingAddress = null;
        $defaultBillingAddress = null;
        $shippingAddresses = collect();
        $billingAddresses = collect();

        if ($user) {
            $defaultShippingAddress = $user->default_shipping_address;
            $defaultBillingAddress = $user->default_billing_address;

            $shippingAddresses = $user->addresses()
                ->where('type', 'shipping')
                ->orderByDesc('is_default')
                ->get();

            $billingAddresses = $user->addresses()
                ->where('type', 'billing')
                ->orderByDesc('is_default')
                ->get();
        }

        return view('pages.checkout.index', [
            'cart' => $cart,
            'user' => $user,
            'isGuest' => !$user,
            'defaultShippingAddress' => $defaultShippingAddress,
            'defaultBillingAddress' => $defaultBillingAddress,
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
            'shippingCost' => $shippingCost,
            'total' => $cart['total'] + $shippingCost,
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $result = $this->orderService->createOrder($validated);

        if ($result['success']) {
            return redirect()->route('orders.confirmation', $result['order']->order_number)
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function confirmation(string $orderNumber): View|RedirectResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            $order = $this->orderService->getOrderByNumber($orderNumber);

            $guestOrderToken = session('guest_order_token');
            if (!$order || $order->guest_token !== $guestOrderToken) {
                return redirect()->route('home')
                    ->with('error', 'Order not found.');
            }
        } else {
            $order = $this->orderService->getOrderByNumber($orderNumber, $userId);

            if (!$order) {
                return redirect()->route('home')
                    ->with('error', 'Order not found.');
            }
        }

        return view('pages.checkout.confirmation', [
            'order' => $order,
        ]);
    }

    public function index(): View
    {
        $orders = $this->orderService->getUserOrders();

        return view('pages.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show(string $orderNumber): View|RedirectResponse
    {
        $order = $this->orderService->getOrderByNumber($orderNumber, Auth::id());

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found.');
        }

        return view('pages.orders.show', [
            'order' => $order,
        ]);
    }

    public function cancel(string $orderNumber): RedirectResponse
    {
        $order = $this->orderService->getOrderByNumber($orderNumber, Auth::id());

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found.');
        }

        $result = $this->orderService->cancelOrder($order);

        return redirect()->route('orders.show', $orderNumber)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
