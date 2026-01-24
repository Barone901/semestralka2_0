@props(['variant' => 'primary'])

@php
$baseClasses = 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';

$variantClasses = match($variant) {
    'primary' => 'bg-gray-800 border-transparent text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-indigo-500',
    'secondary' => 'bg-white border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-indigo-500 disabled:opacity-25',
    'danger' => 'bg-red-600 border-transparent text-white hover:bg-red-500 active:bg-red-700 focus:ring-red-500',
    default => 'bg-gray-800 border-transparent text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-indigo-500',
};

$defaultType = $variant === 'secondary' ? 'button' : 'submit';
@endphp

<button {{ $attributes->merge(['type' => $defaultType, 'class' => $baseClasses . ' ' . $variantClasses]) }}>
    {{ $slot }}
</button>

