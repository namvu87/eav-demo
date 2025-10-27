@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $entity->entity_name }}</h1>
                    <p class="text-sm text-gray-500">{{ $entity->entity_code }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('eav.edit', $entity->entity_id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <h2 class="text-lg font-medium mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm text-gray-500">Entity Type</h3>
                    <p class="mt-1 text-gray-900">{{ $entity->entityType->type_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Status</h3>
                    <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full {{ $entity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $entity->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        @if(count($attributes) > 0)
        <div class="px-6 py-4 border-t border-gray-200">
            <h2 class="text-lg font-medium mb-4">Attributes</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($attributes as $item)
                    <div>
                        <h3 class="text-sm text-gray-500">{{ $item['attribute']->attribute_label }}</h3>
                        <p class="mt-1 text-gray-900">{{ $item['display_value'] ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
