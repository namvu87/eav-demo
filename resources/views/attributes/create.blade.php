@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Create New Attribute</h1>
            </div>

            <form method="POST" action="{{ route('attributes.store') }}" class="px-4 py-5 sm:p-6">
                @csrf
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Code *</label>
                            <input type="text" name="attribute_code" value="{{ old('attribute_code') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('attribute_code')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Label *</label>
                            <input type="text" name="attribute_label" value="{{ old('attribute_label') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('attribute_label')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Backend Type *</label>
                            <select name="backend_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="varchar">Varchar</option>
                                <option value="text">Text</option>
                                <option value="int">Int</option>
                                <option value="decimal">Decimal</option>
                                <option value="datetime">DateTime</option>
                                <option value="file">File</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Frontend Input *</label>
                            <select name="frontend_input" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Select</option>
                                <option value="multiselect">Multiselect</option>
                                <option value="yesno">Yes/No</option>
                                <option value="file">File</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entity Type</label>
                            <select name="entity_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Shared</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type->entity_type_id }}" {{ old('entity_type_id', $entityTypeId) == $type->entity_type_id ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Group</label>
                            <select name="group_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">No Group</option>
                                @foreach($attributeGroups as $group)
                                    <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" value="1" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Required</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_unique" value="1" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Unique</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('attributes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Attribute
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
