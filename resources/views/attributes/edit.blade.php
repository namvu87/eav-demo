@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Edit Attribute</h1>
            </div>

            <form method="POST" action="{{ route('attributes.update', $attribute->attribute_id) }}" class="px-4 py-5 sm:p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Code *</label>
                            <input type="text" name="attribute_code" value="{{ old('attribute_code', $attribute->attribute_code) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('attribute_code')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Attribute Label *</label>
                            <input type="text" name="attribute_label" value="{{ old('attribute_label', $attribute->attribute_label) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('attribute_label')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Backend Type *</label>
                            <select name="backend_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="varchar" {{ $attribute->backend_type == 'varchar' ? 'selected' : '' }}>Varchar</option>
                                <option value="text" {{ $attribute->backend_type == 'text' ? 'selected' : '' }}>Text</option>
                                <option value="int" {{ $attribute->backend_type == 'int' ? 'selected' : '' }}>Int</option>
                                <option value="decimal" {{ $attribute->backend_type == 'decimal' ? 'selected' : '' }}>Decimal</option>
                                <option value="datetime" {{ $attribute->backend_type == 'datetime' ? 'selected' : '' }}>DateTime</option>
                                <option value="file" {{ $attribute->backend_type == 'file' ? 'selected' : '' }}>File</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Frontend Input *</label>
                            <select name="frontend_input" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="text" {{ $attribute->frontend_input == 'text' ? 'selected' : '' }}>Text</option>
                                <option value="textarea" {{ $attribute->frontend_input == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                <option value="select" {{ $attribute->frontend_input == 'select' ? 'selected' : '' }}>Select</option>
                                <option value="multiselect" {{ $attribute->frontend_input == 'multiselect' ? 'selected' : '' }}>Multiselect</option>
                                <option value="yesno" {{ $attribute->frontend_input == 'yesno' ? 'selected' : '' }}>Yes/No</option>
                                <option value="file" {{ $attribute->frontend_input == 'file' ? 'selected' : '' }}>File</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entity Type</label>
                            <select name="entity_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Shared</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type->entity_type_id }}" {{ $attribute->entity_type_id == $type->entity_type_id ? 'selected' : '' }}>
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
                                    <option value="{{ $group->group_id }}" {{ $attribute->group_id == $group->group_id ? 'selected' : '' }}>
                                        {{ $group->group_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" value="1" {{ $attribute->is_required ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Required</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_unique" value="1" {{ $attribute->is_unique ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Unique</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('attributes.show', $attribute->attribute_id) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Update Attribute
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
