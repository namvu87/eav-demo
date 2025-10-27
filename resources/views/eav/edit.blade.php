@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('eav.show', $entity->entity_id) }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Entity</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Update entity: {{ $entity->entity_name }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('eav.update', $entity->entity_id) }}" class="px-4 py-5 sm:p-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Entity Type
                                </label>
                                <input
                                    type="text"
                                    value="{{ $entity->entityType->type_name }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Entity Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="entity_code"
                                    value="{{ old('entity_code', $entity->entity_code) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('entity_code') border-red-300 @enderror"
                                    required
                                />
                                @error('entity_code')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Entity Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="entity_name"
                                    value="{{ old('entity_name', $entity->entity_name) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('entity_name') border-red-300 @enderror"
                                    required
                                />
                                @error('entity_name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Parent Entity
                                </label>
                                <select
                                    name="parent_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">No Parent</option>
                                    @foreach($allEntities as $parent)
                                        <option value="{{ $parent->entity_id }}" {{ old('parent_id', $entity->parent_id) == $parent->entity_id ? 'selected' : '' }}>
                                            {{ $parent->entity_name }} ({{ $parent->entityType->type_name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >{{ old('description', $entity->description) }}</textarea>
                            </div>

                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', $entity->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Sort Order
                                </label>
                                <input
                                    type="number"
                                    name="sort_order"
                                    value="{{ old('sort_order', $entity->sort_order ?? 0) }}"
                                    min="0"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Attributes -->
                    @if(count($attributes) > 0)
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            Attributes for {{ $entity->entityType->type_name }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($attributes as $attribute)
                                @php
                                    $fieldName = 'attr_' . $attribute->attribute_id;
                                    $currentValue = $currentAttributes[$attribute->attribute_code] ?? null;
                                    $value = old($fieldName) ?? $currentValue;
                                    $errorClass = $errors->has($fieldName) ? 'border-red-300' : '';
                                @endphp
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $attribute->attribute_label }}
                                        @if($attribute->is_required)
                                            <span class="text-red-500 ml-1">*</span>
                                        @endif
                                    </label>

                                    @if($attribute->frontend_input === 'text')
                                        <input
                                            type="text"
                                            name="{{ $fieldName }}"
                                            value="{{ is_array($value) ? implode(', ', $value) : $value }}"
                                            placeholder="{{ $attribute->placeholder ?? '' }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errorClass }}"
                                            @if($attribute->is_required) required @endif
                                        />
                                    @elseif($attribute->frontend_input === 'textarea')
                                        <textarea
                                            name="{{ $fieldName }}"
                                            rows="3"
                                            placeholder="{{ $attribute->placeholder ?? '' }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errorClass }}"
                                            @if($attribute->is_required) required @endif
                                        >{{ is_array($value) ? implode(', ', $value) : $value }}</textarea>
                                    @elseif($attribute->frontend_input === 'select')
                                        <select
                                            name="{{ $fieldName }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errorClass }}"
                                            @if($attribute->is_required) required @endif
                                        >
                                            <option value="">Select {{ $attribute->attribute_label }}</option>
                                            @foreach($attribute->options as $option)
                                                <option value="{{ $option->option_id }}" {{ $value == $option->option_id ? 'selected' : '' }}>
                                                    {{ $option->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($attribute->frontend_input === 'multiselect')
                                        <div class="space-y-2">
                                            @foreach($attribute->options as $option)
                                                <label class="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        name="{{ $fieldName }}[]"
                                                        value="{{ $option->option_id }}"
                                                        {{ (is_array($value) && in_array($option->option_id, $value)) || $value == $option->option_id ? 'checked' : '' }}
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                    />
                                                    <span class="ml-2 text-sm text-gray-700">{{ $option->value }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($attribute->frontend_input === 'yesno')
                                        <div class="flex space-x-4">
                                            <label class="flex items-center">
                                                <input
                                                    type="radio"
                                                    name="{{ $fieldName }}"
                                                    value="1"
                                                    {{ $value === '1' || $value === 1 || $value === true ? 'checked' : '' }}
                                                    class="text-indigo-600 focus:ring-indigo-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-700">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input
                                                    type="radio"
                                                    name="{{ $fieldName }}"
                                                    value="0"
                                                    {{ $value === '0' || $value === 0 || $value === false ? 'checked' : '' }}
                                                    class="text-indigo-600 focus:ring-indigo-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-700">No</span>
                                            </label>
                                        </div>
                                    @elseif($attribute->frontend_input === 'file')
                                        <div class="space-y-2">
                                            @if($value)
                                                <p class="text-xs text-gray-500">Current file: {{ is_array($value) ? ($value['name'] ?? $value['path']) : $value }}</p>
                                            @endif
                                            <input
                                                type="file"
                                                name="{{ $fieldName }}"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errorClass }}"
                                            />
                                        </div>
                                    @else
                                        <input
                                            type="{{ $attribute->frontend_input === 'number' ? 'number' : 'text' }}"
                                            name="{{ $fieldName }}"
                                            value="{{ is_array($value) ? implode(', ', $value) : $value }}"
                                            placeholder="{{ $attribute->placeholder ?? '' }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errorClass }}"
                                            @if($attribute->is_required) required @endif
                                        />
                                    @endif

                                    @if($attribute->help_text)
                                        <p class="text-xs text-gray-500">{{ $attribute->help_text }}</p>
                                    @endif

                                    @error($fieldName)
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                    <a href="{{ route('eav.show', $entity->entity_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Update Entity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection