@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('attributes.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $attribute->attribute_label }}</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $attribute->attribute_code }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('attributes.edit', $attribute->attribute_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Attribute Details</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Label</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attribute->attribute_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attribute->attribute_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Entity Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attribute->entityType->type_name ?? 'Shared' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Frontend Input</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attribute->frontend_input }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Backend Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $attribute->backend_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Required</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $attribute->is_required ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $attribute->is_required ? 'Yes' : 'No' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
