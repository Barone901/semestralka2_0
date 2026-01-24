<x-layouts.default-layout>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid gap-8 lg:grid-cols-2">

            {{-- Image --}}
            <div class="overflow-hidden rounded-2xl border bg-white">

                    @if($product->image_url)
                        <img
                            src="{{ str_starts_with($product->image_url, 'http')
                                ? $product->image_url
                                : asset('storage/' . $product->image_url) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        />
                    @endif

            </div>

            {{-- Info --}}
            <div>
                <div class="text-sm text-gray-500">
                    Category:
                    <span class="font-medium text-gray-900">
                        {{ $product->category?->name ?? '—' }}
                    </span>
                </div>

                <h1 class="mt-2 text-3xl font-bold tracking-tight">{{ $product->name }}</h1>

                <div class="mt-4 flex items-center gap-4">
                    <div class="text-2xl font-bold">
                        {{ $product->formatted_price }}
                    </div>

                    <x-shop.stock-badge :stock="$product->stock" />
                </div>

                <div class="mt-6 text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if($product->stock > 0)
                        <div class="flex items-center gap-2">
                            <label for="quantity" class="text-sm text-gray-600">Quantity:</label>
                            <input
                                type="number"
                                id="quantity"
                                value="1"
                                min="1"
                                max="{{ $product->stock }}"
                                class="w-16 rounded-lg border px-2 py-1 text-center text-sm"
                            />
                        </div>
                        <button
                            type="button"
                            data-add-to-cart="{{ $product->id }}"
                            onclick="this.dataset.quantity = document.getElementById('quantity').value"
                            class="rounded-xl bg-black px-5 py-3 text-sm text-white hover:opacity-90 transition-opacity"
                        >
                            Add to Cart
                        </button>
                    @else
                        <button disabled class="rounded-xl bg-gray-300 px-5 py-3 text-sm text-gray-500 cursor-not-allowed">
                            Out of Stock
                        </button>
                    @endif
                    <a href="{{ route('products.index') }}" class="rounded-xl border px-5 py-3 text-sm hover:bg-gray-50">
                        Back
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-layouts.default-layout>
