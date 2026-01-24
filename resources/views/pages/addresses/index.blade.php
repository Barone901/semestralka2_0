<x-layouts.default-layout title="My Addresses">

    <div class="py-8">

        {{-- Page Header --}}
        <x-ui.page-header title="My Addresses" subtitle="Manage your shipping and billing addresses." />

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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Shipping Addresses --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <svg class="inline-block w-6 h-6 mr-2 text-indigo-600" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Shipping Addresses
                    </h2>
                    <a href="{{ route('addresses.create', ['type' => 'shipping']) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add
                    </a>
                </div>

                @if($shippingAddresses->isEmpty())
                    <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <p class="text-gray-500 mb-4">You don't have any shipping addresses yet</p>
                        <a href="{{ route('addresses.create', ['type' => 'shipping']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Add shipping address →
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($shippingAddresses as $address)
                            <div class="bg-white rounded-xl shadow-sm border {{ $address->is_default ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-gray-200' }} p-5">
                                @if($address->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mb-3">
                                        <svg class="w-3 h-3 mr-1" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Default
                                    </span>
                                @endif

                                <div class="text-gray-900">
                                    <p class="font-semibold">{{ $address->full_name }}</p>
                                    <p class="text-gray-600">{{ $address->street }}</p>
                                    <p class="text-gray-600">{{ $address->postal_code }} {{ $address->city }}</p>
                                    <p class="text-gray-600">{{ $address->country }}</p>
                                    @if($address->phone)
                                        <p class="text-gray-500 text-sm mt-2">Phone: {{ $address->phone }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('addresses.edit', $address) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                        Edit
                                    </a>
                                    @if(!$address->is_default)
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('addresses.setDefault', $address) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-gray-600 hover:text-indigo-600 font-medium">
                                                Set as default
                                            </button>
                                        </form>
                                    @endif
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Billing Addresses --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <svg class="inline-block w-6 h-6 mr-2 text-green-600" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Billing Addresses
                    </h2>
                    <a href="{{ route('addresses.create', ['type' => 'billing']) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add
                    </a>
                </div>

                @if($billingAddresses->isEmpty())
                    <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 mb-4">You don't have any billing addresses yet</p>
                        <a href="{{ route('addresses.create', ['type' => 'billing']) }}" class="text-green-600 hover:text-green-800 font-medium">
                            Add billing address →
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($billingAddresses as $address)
                            <div class="bg-white rounded-xl shadow-sm border {{ $address->is_default ? 'border-green-500 ring-2 ring-green-100' : 'border-gray-200' }} p-5">
                                @if($address->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-3">
                                        <svg class="w-3 h-3 mr-1" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Default
                                    </span>
                                @endif

                                <div class="text-gray-900">
                                    @if($address->company_name)
                                        <p class="font-bold text-gray-900">{{ $address->company_name }}</p>
                                    @endif
                                    <p class="font-semibold">{{ $address->full_name }}</p>
                                    <p class="text-gray-600">{{ $address->street }}</p>
                                    <p class="text-gray-600">{{ $address->postal_code }} {{ $address->city }}</p>
                                    <p class="text-gray-600">{{ $address->country }}</p>

                                    @if($address->ico || $address->dic)
                                        <div class="mt-2 text-sm text-gray-500">
                                            @if($address->ico)<p>Company ID: {{ $address->ico }}</p>@endif
                                            @if($address->dic)<p>Tax ID: {{ $address->dic }}</p>@endif
                                            @if($address->ic_dph)<p>VAT ID: {{ $address->ic_dph }}</p>@endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('addresses.edit', $address) }}" class="text-sm text-green-600 hover:text-green-800 font-medium">
                                        Edit
                                    </a>
                                    @if(!$address->is_default)
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('addresses.setDefault', $address) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-gray-600 hover:text-green-600 font-medium">
                                                Set as default
                                            </button>
                                        </form>
                                    @endif
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Back to Dashboard --}}
        <div class="mt-8 text-center">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Back to Dashboard
            </a>
        </div>
    </div>

</x-layouts.default-layout>

