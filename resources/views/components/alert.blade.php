@props(['type' => 'info', 'title' => '', 'message' => ''])

@php
$colors = [
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800'
];

$icons = [
    'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
    'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
];
@endphp

@if(session($type) || $message)
    <div class="rounded-md {{ $colors[$type] }} border p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="{{ $icons[$type] }}" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                @if($title)
                    <h3 class="text-sm font-medium">{{ $title }}</h3>
                @endif
                <div class="mt-2 text-sm">
                    <p>{{ $message ?: session($type) }}</p>
                </div>
            </div>
        </div>
    </div>
@endif
