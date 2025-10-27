@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('attributes.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Attribute</h1>
                    <p class="text-sm text-gray-500">Add a new attribute to entity types</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('attributes.store') }}" class="px-6 py-4">
            @csrf
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entity Type</label>
                            <select name="entity_type_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Global (All Types)</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type->entity_type_id }}" {{ old('entity_type_id', $entityTypeId) == $type->entity_type_id ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Group</label>
                            <select name="group_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">No Group</option>
                                @foreach($attributeGroups as $group)
                                    <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Code <span class="text-red-500">*</span></label>
                            <input type="text" name="attribute_code" value="{{ old('attribute_code') }}" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('attribute_code') border-red-300 @enderror">
                            @error('attribute_code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Label <span class="text-red-500">*</span></label>
                            <input type="text" name="attribute_label" value="{{ old('attribute_label') }}" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('attribute_label') border-red-300 @enderror">
                            @error('attribute_label')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Backend Type <span class="text-red-500">*</span></label>
                            <select name="backend_type" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="varchar">Varchar</option>
                                <option value="text">Text</option>
                                <option value="int">Integer</option>
                                <option value="decimal">Decimal</option>
                                <option value="datetime">DateTime</option>
                                <option value="file">File</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Frontend Input <span class="text-red-500">*</span></label>
                            <select name="frontend_input" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="number">Number</option>
                                <option value="select">Select</option>
                                <option value="multiselect">Multi Select</option>
                                <option value="yesno">Yes/No</option>
                                <option value="file">File</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Properties -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Properties</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Required</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_unique" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Unique</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_searchable" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Searchable</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_filterable" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Filterable</span>
                        </label>
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <h3 class="text-sm font-medium text-red-800 mb-2">Please correct the following errors:</h3>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('attributes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create Attribute
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
