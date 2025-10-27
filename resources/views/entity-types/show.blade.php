@extends('layouts.app')

@section('content')
<div>
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('entity-types.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $entityType->type_name }}</h1>
                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                            <span>Code: {{ $entityType->type_code }}</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $entityType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $entityType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('entity-types.manage', $entityType->entity_type_id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Manage
                    </a>
                    <a href="{{ route('entity-types.edit', $entityType->entity_type_id) }}" class="border border-gray-300 px-4 py-2 rounded hover:bg-gray-50">
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-4">
            <h2 class="text-lg font-medium mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm text-gray-500">Entity Type ID</h3>
                    <p class="mt-1 text-gray-900">{{ $entityType->entity_type_id }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Created</h3>
                    <p class="mt-1 text-gray-900">{{ $entityType->created_at->format('Y-m-d') }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Updated</h3>
                    <p class="mt-1 text-gray-900">{{ $entityType->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        @if($entityType->description)
        <div class="px-6 py-4 border-t border-gray-200">
            <h2 class="text-lg font-medium mb-2">Description</h2>
            <p class="text-gray-700">{{ $entityType->description }}</p>
        </div>
        @endif

        @if($entityType->attributes && $entityType->attributes->count() > 0)
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium">Attributes</h2>
                <a href="{{ route('attributes.create', ['entity_type_id' => $entityType->entity_type_id]) }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                    Add Attribute
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($entityType->attributes as $attribute)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900">{{ $attribute->attribute_label }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $attribute->attribute_code }} • {{ $attribute->backend_type }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

