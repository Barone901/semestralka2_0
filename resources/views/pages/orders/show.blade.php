<x-layouts.default-layout title="Order {{ $order->order_number }}">

    <div class="py-8">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Order Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
                <p class="text-gray-600 mt-1">Created on {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="order-status-badge inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold" data-status="{{ $order->status }}">
                    {{ $order->status_text }}
                </span>
                @if(in_array($order->status, ['pending', 'confirmed']))
                    <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Order Progress --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Status</h2>
                    <div class="order-progress">
                        @php
                            $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
                            $currentIndex = array_search($order->status, $statuses);
                            if ($order->status === 'cancelled') {
                                $currentIndex = -1;
                            }
                        @endphp
                        <div class="flex items-center justify-between">
                            @foreach(['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered'] as $index => $statusLabel)
                                <div class="flex flex-col items-center relative {{ $index < count($statuses) - 1 ? 'flex-1' : '' }}">
                                    <div class="progress-dot w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                                        {{ $index <= $currentIndex ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-500 text-white' : '' }}">
                                        @if($index < $currentIndex)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-xs mt-2 text-center {{ $index <= $currentIndex ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($index < count($statuses) - 1)
                                        <div class="progress-line absolute top-5 left-full w-full h-0.5 {{ $index < $currentIndex ? 'bg-indigo-600' : 'bg-gray-200' }}" style="transform: translateX(-50%);"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($order->status === 'cancelled')
                            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-center">
                                <span class="text-red-700 font-medium">Order has been cancelled</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="p-6 flex items-center gap-4">
                                @if($item->product && $item->product->image_url)
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                         alt="{{ $item->product_name }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $item->product_name }}</h4>
                                    <p class="text-gray-500 text-sm">Unit price: {{ $item->formatted_price }}</p>
                                    <p class="text-gray-500 text-sm">Quantity: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900">{{ $item->formatted_subtotal }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Order Summary --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-gray-900">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                            <span class="text-gray-900">Total</span>
                            <span class="text-indigo-600">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method</span>
                            <span class="font-medium text-gray-900">{{ $order->payment_method_text }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Status</span>
                            <span class="font-medium text-gray-900">{{ $order->payment_status_text }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                    <div class="text-sm text-gray-600">
                        <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                        <p class="mt-2">{{ $order->shipping_phone }}</p>
                        <p>{{ $order->shipping_email }}</p>
                    </div>
                </div>

                @if($order->note)
                {{-- Order Note --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Note</h2>
                    <p class="text-sm text-gray-600">{{ $order->note }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Back to Orders --}}
        <div class="mt-8 text-center">
            <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Back to My Orders
            </a>
        </div>
    </div>

</x-layouts.default-layout>

