@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Attributes</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage your EAV attributes
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            onclick="toggleFilters()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </button>
                        <a href="{{ route('attributes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Attribute
                        </a>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="mt-6">
                    <form method="GET" action="{{ route('attributes.index') }}" class="flex space-x-4">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Search attributes..."
                                    value="{{ request('search') }}"
                                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Search
                        </button>
                    </form>

                    <!-- Advanced Filters -->
                    <div id="filters" class="mt-4 p-4 bg-gray-50 rounded-md hidden">
                        <form method="GET" action="{{ route('attributes.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Entity Type</label>
                                <select name="entity_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Types</option>
                                    @foreach($entityTypes as $type)
                                        <option value="{{ $type->entity_type_id }}" {{ request('entity_type_id') == $type->entity_type_id ? 'selected' : '' }}>
                                            {{ $type->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Attribute Group</label>
                                <select name="group_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Groups</option>
                                    @foreach($attributeGroups as $group)
                                        <option value="{{ $group->group_id }}" {{ request('group_id') == $group->group_id ? 'selected' : '' }}>
                                            {{ $group->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Apply Filters
                                </button>
                                <a href="{{ route('attributes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Attributes Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attribute</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Input</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Options</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attributes as $attribute)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $attribute->attribute_label }}</div>
                                        <div class="text-sm text-gray-500">{{ $attribute->attribute_code }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $attribute->entityType->type_name ?? 'Shared' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $attribute->frontend_input }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ $attribute->backend_type }})</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attribute->options->count() > 0)
                                        <span class="text-sm text-indigo-600">{{ $attribute->options->count() }} options</span>
                                    @else
                                        <span class="text-sm text-gray-400">No options</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('attributes.show', $attribute->attribute_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            View
                                        </a>
                                        <a href="{{ route('attributes.edit', $attribute->attribute_id) }}" class="text-yellow-600 hover:text-yellow-900">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('attributes.destroy', $attribute->attribute_id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this attribute?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No attributes found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($attributes->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ $attributes->firstItem() }} to {{ $attributes->lastItem() }} of {{ $attributes->total() }} results
                        </div>
                        <div class="flex space-x-1">
                            {{ $attributes->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleFilters() {
    const filters = document.getElementById('filters');
    filters.classList.toggle('hidden');
}
</script>
@endsection
