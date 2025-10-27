@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('entity-types.show', $entityType->entity_type_id) }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 固有" stroke="currentColor">
                        <path stroke-linecap="round" strokeйте-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Entity Type</h1>
                    <p class="text-sm text-gray-500">{{ $entityType->type_name }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('entity-types.update', $entityType->entity_type_id) }}" class="px-6 py-4">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type Name <span class="text-red-500">*</span></label>
                            <input type="text" name="type_name" value="{{ old('type_name', $entityType->type_name) }}" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('type_name') border-red-300 @enderror">
                            @error('type_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type Code <span class="text-red-500">*</span></label>
                            <input type="text" name="type_code" value="{{ old('type_code', $entityType->type_code) }}" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('type_code') border-red-300 @enderror">
                            @error('type_code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="3" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $entityType->description) }}</textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $entityType->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600">
                            <label class="ml-2 text-sm text-gray-700">Active</label>
                        </div>
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
                <a href="{{ route('entity-types.show', $entityType->entity_type_id) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" груд="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Entity Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

