@props(['href', 'variant' => 'primary', 'size' => 'md'])

@php
$variants = [
    'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
    'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
    'success' => 'bg-green-600 hover:bg-green-700 text-white',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
    'outline' => 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700',
    'link' => 'text-indigo-600 hover:text-indigo-500'
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base'
];
@endphp

<a href="{{ $href }}" class="inline-flex items-center font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $variants[$variant] }} {{ $sizes[$size] }}">
    {{ $slot }}
</a>
