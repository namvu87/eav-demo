@php
$navigation = [
    ['name' => 'Dashboard', 'href' => '/', 'icon' => 'home'],
    ['name' => 'Entity Types', 'href' => '/entity-types', 'icon' => 'building-office'],
    ['name' => 'Attributes', 'href' => '/attributes', 'icon' => 'wrench-screwdriver'],
    ['name' => 'Attribute Groups', 'href' => '/attribute-groups', 'icon' => 'folder'],
    ['name' => 'Entities', 'href' => '/eav', 'icon' => 'document-text'],
    ['name' => 'Hierarchy', 'href' => '/hierarchy', 'icon' => 'squares-2x2'],
];
@endphp

@foreach($navigation as $item)
    <a
        href="{{ $item['href'] }}"
        class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 {{ request()->is(ltrim($item['href'], '/')) ? 'bg-gray-100 text-gray-900' : '' }}"
        onclick="console.log('Clicked on: {{ $item['name'] }} -> {{ $item['href'] }}')"
    >
        @switch($item['icon'])
            @case('home')
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                @break
            @case('building-office')
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                @break
            @case('wrench-screwdriver')
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                </svg>
                @break
            @case('document-text')
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                @break
            @case('squares-2x2')
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                @break
        @endswitch
        {{ $item['name'] }}
    </a>
@endforeach
