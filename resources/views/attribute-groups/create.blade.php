@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('attribute-groups.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create Attribute Group</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Create a new attribute group to organize attributes into tabs
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('attribute-groups.store') }}" class="px-4 py-5 sm:p-6">
                @csrf
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Entity Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="entity_type_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('entity_type_id') border-red-300 @enderror"
                                    required
                                >
                                    <option value="">Select Entity Type</option>
                                    @foreach($entityTypes as $type)
                                        <option value="{{ $type->entity_type_id }}" {{ old('entity_type_id', $entityTypeId) == $type->entity_type_id ? 'selected' : '' }}>
                                            {{ $type->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('entity_type_id')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Group Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="group_code"
                                    value="{{ old('group_code') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('group_code') border-red-300 @enderror"
                                    placeholder="e.g., general, technical, advanced"
                                    required
                                />
                                @error('group_code')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Unique identifier for the group</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Group Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="group_name"
                                    value="{{ old('group_name') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('group_name') border-red-300 @enderror"
                                    placeholder="e.g., General Information, Technical Details"
                                    required
                                />
                                @error('group_name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Display name for the group</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Sort Order
                                </label>
                                <input
                                    type="number"
                                    name="sort_order"
                                    value="{{ old('sort_order', 0) }}"
                                    min="0"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <p class="text-xs text-gray-500 mt-1">Order of display (lower numbers appear first)</p>
                            </div>

                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Examples -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">Common Attribute Groups</h3>
                        <div class="text-sm text-blue-700 space-y-1">
                            <div><strong>General:</strong> basic_info, general_info, main_info</div>
                            <div><strong>Technical:</strong> technical_specs, technical_details, specifications</div>
                            <div><strong>Advanced:</strong> advanced_settings, advanced_options, configuration</div>
                            <div><strong>Media:</strong> images, media, files, attachments</div>
                            <div><strong>SEO:</strong> seo_settings, meta_info, seo</div>
                        </div>
                    </div>

                    <!-- Error Summary -->
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Please correct the following errors:
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('attribute-groups.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Attribute Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
