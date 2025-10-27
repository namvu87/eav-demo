@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('eav.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $entity->entity_name }}</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $entity->entity_code }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a 
                        href="{{ route('eav.edit', $entity->entity_id) }}" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('eav.destroy', $entity->entity_id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this entity?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Basic Information -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Entity Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $entity->entityType->type_name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Entity Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $entity->entity_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $entity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $entity->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    @if($entity->parent)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Parent Entity</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('eav.show', $entity->parent->entity_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $entity->parent->entity_name }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if($entity->description)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $entity->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Attributes -->
            @if(count($attributes) > 0)
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Attributes</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    @foreach($attributes as $item)
                        <div class="{{ isset($item['value']) && !empty($item['value']) ? '' : 'opacity-50' }}">
                            <dt class="text-sm font-medium text-gray-500">{{ $item['attribute']->attribute_label }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($item['attribute']->backend_type === 'file' && is_array($item['value']))
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <a href="{{ asset('storage/' . $item['value']['path']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $item['value']['name'] ?? $item['value']['path'] }}
                                        </a>
                                    </div>
                                @elseif($item['attribute']->backend_type === 'file' && is_array($item['value']) && isset($item['value'][0]))
                                    <div class="space-y-2">
                                        @foreach($item['value'] as $file)
                                            <div class="flex items-center space-x-2">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $file['name'] ?? $file['path'] }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{ $item['display_value'] ?? '-' }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            @else
            <div class="px-4 py-5 sm:p-6">
                <p class="text-sm text-gray-500 text-center">No attributes defined for this entity type.</p>
            </div>
            @endif
        </div>

        @if($entity->children()->count() > 0)
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Child Entities</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($entity->children as $child)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('eav.show', $child->entity_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                            {{ $child->entity_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $child->entity_code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('eav.show', $child->entity_id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
