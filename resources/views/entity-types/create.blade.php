@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('entity-types.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create Entity Type</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Define a new entity type with its structure and attributes
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('entity-types.store') }}" class="px-4 py-5 sm:p-6">
                @csrf
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Type Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="type_name"
                                    value="{{ old('type_name') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('type_name') border-red-300 @enderror"
                                    placeholder="Enter type name"
                                    required
                                />
                                @error('type_name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Type Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="type_code"
                                    value="{{ old('type_code') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('type_code') border-red-300 @enderror"
                                    placeholder="Enter type code"
                                    required
                                />
                                @error('type_code')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
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

                    <!-- Attribute Groups -->
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-medium text-gray-900">Attribute Groups</h2>
                                <a href="{{ route('attribute-groups.create') }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Group
                                </a>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Organize attributes into groups (tabs)</p>
                        </div>
                        <div class="p-4">
                            <div id="attributeGroupsContainer" class="space-y-3">
                                <!-- Attribute groups will be loaded here -->
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="loadAttributeGroups()" class="text-sm text-indigo-600 hover:text-indigo-500">
                                    Refresh Groups
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Attributes Selection -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Available Attributes</h2>
                            <div class="flex space-x-2">
                                <button type="button" onclick="openQuickCreateModal()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Quick Create
                                </button>
                                <a href="{{ route('attributes.create') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                                    </svg>
                                    Full Create
                                </a>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">
                            Select which attributes should be available for this entity type. Use Quick Create for common attributes.
                        </p>
                        
                        <!-- Search and Filter -->
                        <div class="mb-4">
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <div class="relative">
                                        <svg class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input
                                            type="text"
                                            id="attributeSearch"
                                            placeholder="Search attributes..."
                                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-indigo-500 focus:border-indigo-500"
                                            onkeyup="filterAttributes()"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <select id="attributeTypeFilter" onchange="filterAttributes()" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Types</option>
                                        <option value="text">Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="select">Select</option>
                                        <option value="multiselect">Multiselect</option>
                                        <option value="yesno">Yes/No</option>
                                        <option value="file">File</option>
                                        <option value="number">Number</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        @if($attributes->count() > 0)
                            <!-- Attributes Table -->
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <input type="checkbox" id="selectAll" onchange="toggleAllAttributes()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attribute</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attributesTableBody" class="bg-white divide-y divide-gray-200">
                                            @foreach($attributes as $attribute)
                                                <tr class="attribute-row hover:bg-gray-50" data-search="{{ strtolower($attribute->attribute_label . ' ' . $attribute->attribute_code . ' ' . $attribute->frontend_input) }}">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <input
                                                            type="checkbox"
                                                            name="attributes[]"
                                                            value="{{ $attribute->attribute_id }}"
                                                            {{ in_array($attribute->attribute_id, old('attributes', [])) ? 'checked' : '' }}
                                                            class="attribute-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        />
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $attribute->attribute_label }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500 font-mono">{{ $attribute->attribute_code }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ ucfirst($attribute->frontend_input) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                                            {{ $attribute->help_text ?: 'No description' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($attribute->is_required)
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                                Required
                                                            </span>
                                                        @else
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                Optional
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Selected Count -->
                                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                                    <div class="text-sm text-gray-700">
                                        <span id="selectedCount">0</span> attributes selected
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                                </svg>
                                <p class="text-sm">No attributes available. Create attributes first or add them after creating the entity type.</p>
                                <div class="mt-4 flex justify-center space-x-3">
                                    <button type="button" onclick="openQuickCreateModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Quick Create Attribute
                                    </button>
                                    <a href="{{ route('attributes.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                                        </svg>
                                        Full Create Attribute
                                    </a>
                                </div>
                            </div>
                        @endif
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
                    <a href="{{ route('entity-types.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Entity Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Create Attribute Modal -->
<div id="quickCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Quick Create Attribute</h3>
            <button onclick="closeQuickCreateModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="quickCreateForm">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Attribute Label *</label>
                    <input type="text" id="quickLabel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Full Name" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Attribute Code *</label>
                    <input type="text" id="quickCode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., full_name" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Input Type *</label>
                    <select id="quickType" onchange="updateQuickCreateFields()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="select">Select</option>
                        <option value="multiselect">Multiselect</option>
                        <option value="yesno">Yes/No</option>
                        <option value="file">File</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="url">URL</option>
                        <option value="date">Date</option>
                        <option value="datetime">DateTime</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="quickDescription" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional description"></textarea>
                </div>
                
                <!-- Dynamic Fields Container -->
                <div id="dynamicFieldsContainer" class="space-y-4">
                    <!-- Fields will be added dynamically based on input type -->
                </div>
                
                <!-- Validation Rules -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Validation Rules</h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="quickRequired" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="quickRequired" class="ml-2 text-sm text-gray-700">Required field</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="quickUnique" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="quickUnique" class="ml-2 text-sm text-gray-700">Unique value</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="quickSearchable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" checked>
                            <label for="quickSearchable" class="ml-2 text-sm text-gray-700">Searchable</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="quickFilterable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" checked>
                            <label for="quickFilterable" class="ml-2 text-sm text-gray-700">Filterable</label>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Options -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Additional Options</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Placeholder Text</label>
                            <input type="text" id="quickPlaceholder" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter placeholder text">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Value</label>
                            <input type="text" id="quickDefaultValue" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter default value">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                            <input type="number" id="quickSortOrder" value="0" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeQuickCreateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    Create & Select
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Ensure DOM is loaded before running scripts
document.addEventListener('DOMContentLoaded', function() {
    console.log('Entity Types Create page loaded');
    
    // Initialize all functions
    initializeQuickCreateModal();
    initializeAttributeGroups();
    initializeAttributeSelection();
});

// Quick Create Modal Functions
function initializeQuickCreateModal() {
    console.log('Initializing Quick Create Modal');
    
    // Check if modal elements exist
    const modal = document.getElementById('quickCreateModal');
    const form = document.getElementById('quickCreateForm');
    const labelInput = document.getElementById('quickLabel');
    
    if (!modal || !form || !labelInput) {
        console.error('Quick Create Modal elements not found');
        return;
    }
    
    console.log('Quick Create Modal elements found');
}

function openQuickCreateModal() {
    console.log('Opening Quick Create Modal');
    
    const modal = document.getElementById('quickCreateModal');
    const labelInput = document.getElementById('quickLabel');
    
    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
    
    // Focus on label input after modal is shown
    setTimeout(() => {
        if (labelInput) {
            labelInput.focus();
        }
    }, 100);
}

function closeQuickCreateModal() {
    console.log('Closing Quick Create Modal');
    
    const modal = document.getElementById('quickCreateModal');
    const form = document.getElementById('quickCreateForm');
    
    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Restore scroll
    
    // Reset form
    if (form) {
        form.reset();
    }
    
    // Clear dynamic fields
    const container = document.getElementById('dynamicFieldsContainer');
    if (container) {
        container.innerHTML = '';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('quickCreateModal');
    if (modal && !modal.classList.contains('hidden')) {
        if (e.target === modal) {
            closeQuickCreateModal();
        }
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('quickCreateModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeQuickCreateModal();
        }
    }
});

// Update Dynamic Fields based on Input Type
function updateQuickCreateFields() {
    const inputType = document.getElementById('quickType').value;
    const container = document.getElementById('dynamicFieldsContainer');
    
    // Clear existing dynamic fields
    container.innerHTML = '';
    
    // Add fields based on input type
    switch (inputType) {
        case 'select':
        case 'multiselect':
            addSelectOptionsFields(container);
            break;
        case 'file':
            addFileFields(container);
            break;
        case 'number':
            addNumberFields(container);
            break;
        case 'text':
        case 'textarea':
            addTextValidationFields(container);
            break;
        case 'email':
            addEmailFields(container);
            break;
        case 'url':
            addUrlFields(container);
            break;
        case 'date':
        case 'datetime':
            addDateFields(container);
            break;
    }
}

// Add Select Options Fields
function addSelectOptionsFields(container) {
    const optionsHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Select Options</h4>
            <div id="selectOptionsContainer" class="space-y-2">
                <div class="flex items-center space-x-2">
                    <input type="text" placeholder="Option label" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="text" placeholder="Option value" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="button" onclick="removeSelectOption(this)" class="text-red-600 hover:text-red-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <button type="button" onclick="addSelectOption()" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500">
                + Add Option
            </button>
        </div>
    `;
    container.innerHTML = optionsHtml;
}

// Add File Fields
function addFileFields(container) {
    const fileHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">File Upload Settings</h4>
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Multi-File Upload Configuration</span>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-red-500">*</span> Số lượng file tải lên
                            </label>
                            <div class="mt-1 flex items-center space-x-2">
                                <input type="number" id="quickMaxFileCount" value="3" min="1" max="20" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="text-sm text-gray-500">files</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Tối đa số lượng file có thể upload cùng lúc</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-red-500">*</span> Loại file được phép
                            </label>
                            <div class="mt-1">
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="file-extension-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="jpg" checked>
                                        <span class="ml-2 text-sm text-gray-700">JPG</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="file-extension-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="png" checked>
                                        <span class="ml-2 text-sm text-gray-700">PNG</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="file-extension-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="pdf">
                                        <span class="ml-2 text-sm text-gray-700">PDF</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="file-extension-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="doc">
                                        <span class="ml-2 text-sm text-gray-700">DOC</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="file-extension-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="docx">
                                        <span class="ml-2 text-sm text-gray-700">DOCX</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="file-extension-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="xls">
                                        <span class="ml-2 text-sm text-gray-700">XLS</span>
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <input type="text" id="quickCustomExtensions" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Hoặc nhập extensions khác (VD: zip,rar,txt)">
                                    <p class="text-xs text-gray-500 mt-1">Các extension khác nhau bằng dấu phẩy</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-red-500">*</span> Kích thước file tối đa
                            </label>
                            <div class="mt-1 flex items-center space-x-2">
                                <input type="number" id="quickMaxFileSize" value="1024" min="1" max="10240" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <select id="quickFileSizeUnit" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="KB">KB</option>
                                    <option value="MB" selected>MB</option>
                                    <option value="GB">GB</option>
                                </select>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Kích thước tối đa cho mỗi file</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-green-800">Upload Preview</span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="text-sm text-gray-600">
                            <strong>Số file:</strong> <span id="fileCountPreview">3</span> files
                        </div>
                        <div class="text-sm text-gray-600">
                            <strong>Loại file:</strong> <span id="extensionsPreview">JPG, PNG</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <strong>Kích thước:</strong> <span id="sizePreview">1 MB</span> per file
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.innerHTML = fileHtml;
    
    // Add event listeners for real-time preview
    document.getElementById('quickMaxFileCount').addEventListener('input', updateFilePreview);
    document.getElementById('quickMaxFileSize').addEventListener('input', updateFilePreview);
    document.getElementById('quickFileSizeUnit').addEventListener('change', updateFilePreview);
    document.querySelectorAll('.file-extension-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateFilePreview);
    });
    document.getElementById('quickCustomExtensions').addEventListener('input', updateFilePreview);
}

// Add Number Fields
function addNumberFields(container) {
    const numberHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Number Settings</h4>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Value</label>
                    <input type="number" id="quickMinValue" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Value</label>
                    <input type="number" id="quickMaxValue" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="999999">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Decimal Places</label>
                    <input type="number" id="quickDecimalPlaces" value="2" min="0" max="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Step</label>
                    <input type="number" id="quickStep" value="1" min="0.01" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    `;
    container.innerHTML = numberHtml;
}

// Add Text Validation Fields
function addTextValidationFields(container) {
    const textHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Text Validation</h4>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Length</label>
                    <input type="number" id="quickMinLength" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Length</label>
                    <input type="number" id="quickMaxLength" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="255">
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700">Pattern (Regex)</label>
                <input type="text" id="quickPattern" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="^[a-zA-Z0-9]+$">
                <p class="text-xs text-gray-500 mt-1">Optional regex pattern for validation</p>
            </div>
        </div>
    `;
    container.innerHTML = textHtml;
}

// Add Email Fields
function addEmailFields(container) {
    const emailHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Email Settings</h4>
            <div class="space-y-3">
                <div class="flex items-center">
                    <input type="checkbox" id="quickEmailVerification" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <label for="quickEmailVerification" class="ml-2 text-sm text-gray-700">Require email verification</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Length</label>
                    <input type="number" id="quickEmailMaxLength" value="255" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    `;
    container.innerHTML = emailHtml;
}

// Add URL Fields
function addUrlFields(container) {
    const urlHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">URL Settings</h4>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Protocol</label>
                    <select id="quickUrlProtocol" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="any">Any Protocol</option>
                        <option value="http">HTTP Only</option>
                        <option value="https">HTTPS Only</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Length</label>
                    <input type="number" id="quickUrlMaxLength" value="500" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    `;
    container.innerHTML = urlHtml;
}

// Add Date Fields
function addDateFields(container) {
    const dateHtml = `
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Date Settings</h4>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Date</label>
                    <input type="date" id="quickMinDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Date</label>
                    <input type="date" id="quickMaxDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-center">
                    <input type="checkbox" id="quickDateRequired" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <label for="quickDateRequired" class="ml-2 text-sm text-gray-700">Must be future date</label>
                </div>
            </div>
        </div>
    `;
    container.innerHTML = dateHtml;
}

// Select Options Management
function addSelectOption() {
    const container = document.getElementById('selectOptionsContainer');
    const newOption = document.createElement('div');
    newOption.className = 'flex items-center space-x-2';
    newOption.innerHTML = `
        <input type="text" placeholder="Option label" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" placeholder="Option value" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        <button type="button" onclick="removeSelectOption(this)" class="text-red-600 hover:text-red-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(newOption);
}

function removeSelectOption(button) {
    button.parentElement.remove();
}

// Quick Create Form Submission
document.getElementById('quickCreateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const inputType = document.getElementById('quickType').value;
    const formData = {
        attribute_label: document.getElementById('quickLabel').value,
        attribute_code: document.getElementById('quickCode').value,
        frontend_input: inputType,
        backend_type: getBackendType(inputType),
        help_text: document.getElementById('quickDescription').value,
        placeholder: document.getElementById('quickPlaceholder').value,
        default_value: document.getElementById('quickDefaultValue').value,
        sort_order: parseInt(document.getElementById('quickSortOrder').value) || 0,
        is_required: document.getElementById('quickRequired').checked,
        is_unique: document.getElementById('quickUnique').checked,
        is_searchable: document.getElementById('quickSearchable').checked,
        is_filterable: document.getElementById('quickFilterable').checked,
        is_active: true,
        validation_rules: getValidationRules(inputType)
    };
    
    // Add options for select/multiselect
    if (inputType === 'select' || inputType === 'multiselect') {
        formData.options = getSelectOptions();
    }
    
    try {
        const response = await fetch('/attributes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            const result = await response.json();
            // Add the new attribute to the table
            addAttributeToTable(result.attribute);
            // Auto-select the new attribute
            selectAttribute(result.attribute.attribute_id);
            closeQuickCreateModal();
        } else {
            const error = await response.json();
            alert('Error creating attribute: ' + (error.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error creating attribute');
    }
});

// Update File Preview
function updateFilePreview() {
    const fileCount = document.getElementById('quickMaxFileCount')?.value || 3;
    const fileSize = document.getElementById('quickMaxFileSize')?.value || 1024;
    const fileSizeUnit = document.getElementById('quickFileSizeUnit')?.value || 'MB';
    
    // Get selected extensions
    const selectedExtensions = [];
    document.querySelectorAll('.file-extension-checkbox:checked').forEach(checkbox => {
        selectedExtensions.push(checkbox.value.toUpperCase());
    });
    
    // Get custom extensions
    const customExtensions = document.getElementById('quickCustomExtensions')?.value;
    if (customExtensions) {
        const customExts = customExtensions.split(',').map(ext => ext.trim().toUpperCase()).filter(ext => ext);
        selectedExtensions.push(...customExts);
    }
    
    // Update preview
    document.getElementById('fileCountPreview').textContent = fileCount;
    document.getElementById('extensionsPreview').textContent = selectedExtensions.length > 0 ? selectedExtensions.join(', ') : 'Chưa chọn';
    document.getElementById('sizePreview').textContent = `${fileSize} ${fileSizeUnit}`;
}

// Get File Extensions
function getFileExtensions() {
    const extensions = [];
    
    // Get checked extensions
    document.querySelectorAll('.file-extension-checkbox:checked').forEach(checkbox => {
        extensions.push(checkbox.value);
    });
    
    // Get custom extensions
    const customExtensions = document.getElementById('quickCustomExtensions')?.value;
    if (customExtensions) {
        const customExts = customExtensions.split(',').map(ext => ext.trim()).filter(ext => ext);
        extensions.push(...customExts);
    }
    
    return extensions.join(',');
}

// Get File Size in KB
function getFileSizeInKB() {
    const fileSize = parseInt(document.getElementById('quickMaxFileSize')?.value || 1024);
    const fileSizeUnit = document.getElementById('quickFileSizeUnit')?.value || 'MB';
    
    switch (fileSizeUnit) {
        case 'KB':
            return fileSize;
        case 'MB':
            return fileSize * 1024;
        case 'GB':
            return fileSize * 1024 * 1024;
        default:
            return fileSize;
    }
}

// Get Validation Rules based on Input Type
function getValidationRules(inputType) {
    const rules = {};
    
    switch (inputType) {
        case 'text':
        case 'textarea':
            const minLength = document.getElementById('quickMinLength')?.value;
            const maxLength = document.getElementById('quickMaxLength')?.value;
            const pattern = document.getElementById('quickPattern')?.value;
            
            if (minLength) rules.min_length = parseInt(minLength);
            if (maxLength) rules.max_length = parseInt(maxLength);
            if (pattern) rules.pattern = pattern;
            break;
            
        case 'number':
            const minValue = document.getElementById('quickMinValue')?.value;
            const maxValue = document.getElementById('quickMaxValue')?.value;
            const decimalPlaces = document.getElementById('quickDecimalPlaces')?.value;
            const step = document.getElementById('quickStep')?.value;
            
            if (minValue) rules.min = parseFloat(minValue);
            if (maxValue) rules.max = parseFloat(maxValue);
            if (decimalPlaces) rules.decimal_places = parseInt(decimalPlaces);
            if (step) rules.step = parseFloat(step);
            break;
            
        case 'file':
            const maxFileCount = document.getElementById('quickMaxFileCount')?.value;
            const allowedExtensions = getFileExtensions();
            const maxFileSizeKB = getFileSizeInKB();
            
            if (maxFileCount) rules.max_file_count = parseInt(maxFileCount);
            if (allowedExtensions) rules.allowed_extensions = allowedExtensions;
            if (maxFileSizeKB) rules.max_file_size_kb = maxFileSizeKB;
            break;
            
        case 'email':
            const emailMaxLength = document.getElementById('quickEmailMaxLength')?.value;
            const emailVerification = document.getElementById('quickEmailVerification')?.checked;
            
            if (emailMaxLength) rules.max_length = parseInt(emailMaxLength);
            if (emailVerification) rules.email_verification = true;
            break;
            
        case 'url':
            const urlProtocol = document.getElementById('quickUrlProtocol')?.value;
            const urlMaxLength = document.getElementById('quickUrlMaxLength')?.value;
            
            if (urlProtocol !== 'any') rules.protocol = urlProtocol;
            if (urlMaxLength) rules.max_length = parseInt(urlMaxLength);
            break;
            
        case 'date':
        case 'datetime':
            const minDate = document.getElementById('quickMinDate')?.value;
            const maxDate = document.getElementById('quickMaxDate')?.value;
            const futureDate = document.getElementById('quickDateRequired')?.checked;
            
            if (minDate) rules.min_date = minDate;
            if (maxDate) rules.max_date = maxDate;
            if (futureDate) rules.future_date = true;
            break;
    }
    
    return rules;
}

// Get Select Options
function getSelectOptions() {
    const options = [];
    const container = document.getElementById('selectOptionsContainer');
    const optionRows = container.querySelectorAll('.flex.items-center.space-x-2');
    
    optionRows.forEach(row => {
        const inputs = row.querySelectorAll('input[type="text"]');
        if (inputs.length >= 2 && inputs[0].value && inputs[1].value) {
            options.push({
                label: inputs[0].value,
                value: inputs[1].value
            });
        }
    });
    
    return options;
}

function getBackendType(frontendType) {
    const mapping = {
        'text': 'varchar',
        'textarea': 'text',
        'select': 'int',
        'multiselect': 'text',
        'yesno': 'int',
        'file': 'varchar',
        'number': 'decimal',
        'email': 'varchar',
        'url': 'varchar',
        'date': 'date',
        'datetime': 'datetime'
    };
    return mapping[frontendType] || 'varchar';
}

function addAttributeToTable(attribute) {
    const tbody = document.getElementById('attributesTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'attribute-row hover:bg-gray-50';
    newRow.setAttribute('data-search', `${attribute.attribute_label.toLowerCase()} ${attribute.attribute_code.toLowerCase()} ${attribute.frontend_input.toLowerCase()}`);
    
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="checkbox" name="attributes[]" value="${attribute.attribute_id}" class="attribute-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" checked>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">${attribute.attribute_label}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500 font-mono">${attribute.attribute_code}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                ${attribute.frontend_input.charAt(0).toUpperCase() + attribute.frontend_input.slice(1)}
            </span>
        </td>
        <td class="px-6 py-4">
            <div class="text-sm text-gray-500 max-w-xs truncate">
                ${attribute.help_text || 'No description'}
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${attribute.is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}">
                ${attribute.is_required ? 'Required' : 'Optional'}
            </span>
        </td>
    `;
    
    tbody.appendChild(newRow);
    updateSelectedCount();
}

function selectAttribute(attributeId) {
    const checkbox = document.querySelector(`input[name="attributes[]"][value="${attributeId}"]`);
    if (checkbox) {
        checkbox.checked = true;
        updateSelectedCount();
    }
}

// Filter Functions
function filterAttributes() {
    const searchTerm = document.getElementById('attributeSearch').value.toLowerCase();
    const typeFilter = document.getElementById('attributeTypeFilter').value.toLowerCase();
    const rows = document.querySelectorAll('.attribute-row');
    
    rows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        const typeMatch = !typeFilter || searchData.includes(typeFilter);
        const searchMatch = !searchTerm || searchData.includes(searchTerm);
        
        if (typeMatch && searchMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Select All Functions
function toggleAllAttributes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.attribute-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.attribute-checkbox:checked');
    const count = checkedBoxes.length;
    document.getElementById('selectedCount').textContent = count;
    
    // Update select all checkbox state
    const selectAll = document.getElementById('selectAll');
    const allCheckboxes = document.querySelectorAll('.attribute-checkbox');
    
    if (count === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (count === allCheckboxes.length) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
    }
}

// Initialize Attribute Groups
function initializeAttributeGroups() {
    console.log('Initializing Attribute Groups');
    loadAttributeGroups();
}

// Load attribute groups
function loadAttributeGroups() {
    const container = document.getElementById('attributeGroupsContainer');
    if (!container) {
        console.log('Attribute groups container not found');
        return;
    }
    
    console.log('Loading attribute groups...');
    
    fetch('/api/attribute-groups')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(groups => {
            console.log('Attribute groups loaded:', groups);
            
            if (groups.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-gray-500">
                        <svg class="h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="text-sm">No attribute groups found.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = groups.map(group => `
                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="attribute_groups[]" 
                               value="${group.group_id}" 
                               id="group_${group.group_id}"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <label for="group_${group.group_id}" class="text-sm font-medium text-gray-900">
                            ${group.group_name}
                        </label>
                        <span class="text-xs text-gray-500 font-mono">${group.group_code}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">${group.attributes_count || 0} attributes</span>
                        <a href="/attribute-groups/${group.group_id}" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading attribute groups:', error);
            container.innerHTML = `
                <div class="text-center py-4 text-red-500">
                    <p class="text-sm">Error loading attribute groups: ${error.message}</p>
                </div>
            `;
        });
}

// Initialize Attribute Selection
function initializeAttributeSelection() {
    console.log('Initializing Attribute Selection');
    updateSelectedCount();
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.attribute-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
}

// Initialize selected count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.attribute-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
});
</script>
@endsection
