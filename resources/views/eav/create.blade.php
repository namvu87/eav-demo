@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('eav.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Entity</h1>
                    <p class="text-sm text-gray-500">Add a new entity to the EAV system</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('eav.store') }}" class="px-6 py-4">
            @csrf
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entity Type <span class="text-red-500">*</span></label>
                            <select name="entity_type_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Entity Type</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type->entity_type_id }}" {{ old('entity_type_id', $entityTypeId) == $type->entity_type_id ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entity Code <span class="text-red-500">*</span></label>
                            <input type="text" name="entity_code" value="{{ old('entity_code') }}" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Entity Name <span class="text-red-500">*</span></label>
                            <input type="text" name="entity_name" value="{{ old('entity_name') }}" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Parent Entity</label>
                            <input type="text" value="{{ $parent->entity_name ?? 'No parent' }}" disabled
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                            <input type="hidden" name="parent_id" value="{{ $parentId }}">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="3" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600">
                            <label class="ml-2 text-sm text-gray-700">Active</label>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <h3 class="text-sm font-medium text-red-800 mb-2">Please correct errors:</h3>
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('eav.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create Entity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
