@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('attributes.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $attribute->attribute_label }}</h1>
                        <p class="text-sm text-gray-500">{{ $attribute->attribute_code }}</p>
                    </div>
                </div>
                <a href="{{ route('attributes.edit', $attribute->attribute_id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Edit
                </a>
            </div>
        </div>

        <div class="px-6 py-4">
            <h2 class="text-lg font-medium mb-4">Attribute Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm text-gray-500">Backend Type</h3>
                    <p class="mt-1 text-gray-900 font-medium">{{ $attribute->backend_type }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Frontend Input</h3>
                    <p class="mt-1 text-gray-900 font-medium">{{ $attribute->frontend_input }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Entity Type</h3>
                    <p class="mt-1 text-gray-900">{{ $attribute->entityType->type_name ?? 'Global' }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Attribute Group</h3>
                    <p class="mt-1 text-gray-900">{{ $attribute->group->group_name ?? 'No Group' }}</p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Properties</h3>
                <div class="flex flex-wrap gap-2">
                    @if($attribute->is_required)
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Required</span>
                    @endif
                    @if($attribute->is_unique)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Unique</span>
                    @endif
                    @if($attribute->is_searchable)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Searchable</span>
                    @endif
                    @if($attribute->is_filterable)
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Filterable</span>
                    @endif
                </div>
            </div>

            @if($attribute->default_value)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900">Default Value</h3>
                <p class="mt-1 text-gray-700">{{ $attribute->default_value }}</p>
            </div>
            @endif

            @if($attribute->help_text)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900">Help Text</h3>
                <p class="mt-1 text-gray-700">{{ $attribute->help_text }}</p>
            </div>
            @endif

            @if($attribute->options && $attribute->options->count() > 0)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Options</h3>
                <div class="space-y-2">
                    @foreach($attribute->options as $option)
                        <div class="bg-gray-50 rounded p-2">
                            <span class="font-medium">{{ $option->value }}</span>
                            <span class="text-sm text-gray-500 ml-2">{{ $option->label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
