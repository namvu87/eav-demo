@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('hierarchy.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            @if($parent)
                                Create Child Entity
                            @else
                                Create Root Entity
                            @endif
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($parent)
                                Add a new child entity under "{{ $parent->entity_name }}"
                            @else
                                Add a new root entity to the hierarchy
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('hierarchy.store') }}" class="px-4 py-5 sm:p-6" enctype="multipart/form-data">
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
                                    id="entity_type_id"
                                    onchange="updateAttributes()"
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
                                    Entity Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="entity_code"
                                    value="{{ old('entity_code') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('entity_code') border-red-300 @enderror"
                                    placeholder="Enter entity code"
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
                                    value="{{ old('entity_name') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('entity_name') border-red-300 @enderror"
                                    placeholder="Enter entity name"
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
                                <input
                                    type="text"
                                    value="{{ $parent->entity_name ?? 'Root Level' }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                />
                                @if($parent)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Parent: {{ $parent->entity_name }} ({{ $parent->entity_code }})
                                    </p>
                                @endif
                                <input type="hidden" name="parent_id" value="{{ $parentId }}" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Enter description"
                                >{{ old('description') }}</textarea>
                            </div>

                            <div class="flex items-center space-x-4">
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
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Attributes -->
                    <div id="attributes-section" class="bg-blue-50 rounded-lg p-4 hidden">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            Attributes for <span id="selected-type-name"></span>
                        </h2>
                        <div id="attributes-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Attributes will be loaded dynamically -->
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
                    <a href="{{ route('hierarchy.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Entity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Get attributes from the server
let attributes = [];

// Load attributes when entity type is selected
async function loadAttributes(entityTypeId) {
    if (!entityTypeId) return [];
    
    try {
        const response = await fetch(`/api/entity-types/${entityTypeId}/attributes`);
        const data = await response.json();
        return data.attributes || [];
    } catch (error) {
        console.error('Error loading attributes:', error);
        return [];
    }
}

function updateAttributes() {
    const entityTypeId = document.getElementById('entity_type_id').value;
    const attributesSection = document.getElementById('attributes-section');
    const attributesContainer = document.getElementById('attributes-container');
    const selectedTypeName = document.getElementById('selected-type-name');
    
    if (!entityTypeId) {
        attributesSection.classList.add('hidden');
        return;
    }
    
    // Find selected entity type name
    const selectedType = @json($entityTypes)->find(type => type.entity_type_id == entityTypeId);
    if (selectedType) {
        selectedTypeName.textContent = selectedType.type_name;
    }
    
    // Load attributes dynamically
    loadAttributes(entityTypeId).then(loadedAttributes => {
        attributes = loadedAttributes;
        
        // Clear container
        attributesContainer.innerHTML = '';
        
        // Generate attribute fields
        attributes.forEach(attribute => {
            const fieldHtml = generateAttributeField(attribute);
            attributesContainer.insertAdjacentHTML('beforeend', fieldHtml);
        });
        
        attributesSection.classList.remove('hidden');
    });
}

function generateAttributeField(attribute) {
    const fieldName = `attr_${attribute.attribute_id}`;
    const oldValue = @json(old());
    const value = oldValue[fieldName] || '';
    const errorClass = @json($errors->has('attr_' . $attribute->attribute_id)) ? 'border-red-300' : '';
    
    let fieldHtml = `
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">
                ${attribute.attribute_label}
                ${attribute.is_required ? '<span class="text-red-500 ml-1">*</span>' : ''}
            </label>
    `;
    
    switch (attribute.frontend_input) {
        case 'text':
            fieldHtml += `
                <input
                    type="text"
                    name="${fieldName}"
                    value="${value}"
                    placeholder="${attribute.placeholder || ''}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${errorClass}"
                    ${attribute.is_required ? 'required' : ''}
                />
            `;
            break;
            
        case 'textarea':
            fieldHtml += `
                <textarea
                    name="${fieldName}"
                    placeholder="${attribute.placeholder || ''}"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${errorClass}"
                    ${attribute.is_required ? 'required' : ''}
                >${value}</textarea>
            `;
            break;
            
        case 'select':
            fieldHtml += `
                <select
                    name="${fieldName}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${errorClass}"
                    ${attribute.is_required ? 'required' : ''}
                >
                    <option value="">Select ${attribute.attribute_label}</option>
            `;
            if (attribute.options) {
                attribute.options.forEach(option => {
                    const selected = value == option.option_id ? 'selected' : '';
                    fieldHtml += `<option value="${option.option_id}" ${selected}>${option.value}</option>`;
                });
            }
            fieldHtml += `</select>`;
            break;
            
        case 'yesno':
            const yesChecked = value === '1' || value === 1 || value === true ? 'checked' : '';
            const noChecked = value === '0' || value === 0 || value === false ? 'checked' : '';
            fieldHtml += `
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input
                            type="radio"
                            name="${fieldName}"
                            value="1"
                            ${yesChecked}
                            class="text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input
                            type="radio"
                            name="${fieldName}"
                            value="0"
                            ${noChecked}
                            class="text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">No</span>
                    </label>
                </div>
            `;
            break;
            
        default:
            fieldHtml += `
                <input
                    type="${attribute.frontend_input === 'number' ? 'number' : 'text'}"
                    name="${fieldName}"
                    value="${value}"
                    placeholder="${attribute.placeholder || ''}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${errorClass}"
                    ${attribute.is_required ? 'required' : ''}
                />
            `;
    }
    
    if (attribute.help_text) {
        fieldHtml += `<p class="text-xs text-gray-500">${attribute.help_text}</p>`;
    }
    
    fieldHtml += `</div>`;
    
    return fieldHtml;
}

// Initialize attributes on page load if entity type is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const entityTypeId = document.getElementById('entity_type_id').value;
    if (entityTypeId) {
        updateAttributes();
    }
});
</script>
@endsection
