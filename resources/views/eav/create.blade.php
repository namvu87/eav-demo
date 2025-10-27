@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('eav.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create New Entity</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Add a new entity to the EAV system
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('eav.store') }}" class="px-4 py-5 sm:p-6" enctype="multipart/form-data">
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
                                    value="{{ $parent->entity_name ?? '' }}"
                                    disabled
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                    placeholder="No parent"
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
                    <a href="{{ route('eav.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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

<style>
.file-upload-container {
    @apply w-full;
}

.file-upload-area {
    @apply border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer transition-colors duration-200 hover:border-indigo-400 hover:bg-indigo-50;
}

.file-upload-area.dragover {
    @apply border-indigo-500 bg-indigo-100;
}

.file-upload-content {
    @apply flex flex-col items-center space-y-4;
}

.file-upload-icon {
    @apply h-12 w-12 text-gray-400;
}

.file-upload-text {
    @apply space-y-2;
}

.file-upload-title {
    @apply text-lg font-medium text-gray-900;
}

.file-upload-subtitle {
    @apply text-sm text-gray-500;
}

.file-upload-extensions {
    @apply text-xs text-gray-400;
}

.file-preview-list {
    @apply mt-4 bg-white border border-gray-200 rounded-lg;
}

.file-preview-header {
    @apply flex justify-between items-center p-4 border-b border-gray-200;
}

.file-preview-title {
    @apply text-sm font-medium text-gray-900;
}

.clear-all-btn {
    @apply flex items-center space-x-1 text-sm text-red-600 hover:text-red-500;
}

.file-list {
    @apply divide-y divide-gray-200;
}

.file-item {
    @apply flex items-center justify-between p-4 hover:bg-gray-50;
}

.file-info {
    @apply flex items-center space-x-3;
}

.file-icon {
    @apply h-8 w-8 text-gray-400;
}

.file-details {
    @apply flex-1;
}

.file-name {
    @apply text-sm font-medium text-gray-900 truncate;
}

.file-size {
    @apply text-xs text-gray-500;
}

.file-actions {
    @apply flex items-center space-x-2;
}

.remove-file-btn {
    @apply text-red-600 hover:text-red-500;
}
</style>

<script>
const attributes = @json($attributes);

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
    
    // Filter attributes for this entity type
    const filteredAttributes = attributes.filter(attr => 
        attr.entity_type_id == entityTypeId || attr.entity_type_id === null
    );
    
    // Clear container
    attributesContainer.innerHTML = '';
    
    // Generate attribute fields
    filteredAttributes.forEach(attribute => {
        const fieldHtml = generateAttributeField(attribute);
        attributesContainer.insertAdjacentHTML('beforeend', fieldHtml);
        
        // Render file upload component for file attributes
        if (attribute.frontend_input === 'file') {
            setTimeout(() => {
                renderFileUploadComponent(attribute);
            }, 100);
        }
    });
    
    attributesSection.classList.remove('hidden');
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
            
        case 'multiselect':
            fieldHtml += `<div class="space-y-2">`;
            if (attribute.options) {
                attribute.options.forEach(option => {
                    const checked = Array.isArray(value) && value.includes(option.option_id) ? 'checked' : '';
                    fieldHtml += `
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="${fieldName}[]"
                                value="${option.option_id}"
                                ${checked}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700">${option.value}</span>
                        </label>
                    `;
                });
            }
            fieldHtml += `</div>`;
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
            
        case 'file':
            fieldHtml += `
                <div id="fileUpload_${attribute.attribute_id}" class="file-upload-wrapper">
                    <!-- File upload component will be rendered here -->
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

// Render file upload component for file attributes
function renderFileUploadComponent(attribute) {
    const container = document.getElementById(`fileUpload_${attribute.attribute_id}`);
    if (!container) return;
    
    const config = {
        max_file_count: attribute.max_file_count || 3,
        max_file_size_kb: attribute.max_file_size_kb || 1024,
        allowed_extensions: attribute.allowed_extensions || 'jpg,png,pdf'
    };
    
    // Create file upload HTML
    const fileUploadHtml = `
        <div class="file-upload-container">
            <div class="file-upload-area" id="fileUploadArea_${attribute.attribute_id}">
                <div class="file-upload-content">
                    <svg class="file-upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <div class="file-upload-text">
                        <p class="file-upload-title">Kéo thả file vào đây hoặc click để chọn</p>
                        <p class="file-upload-subtitle">
                            Tối đa <span id="maxFileCountDisplay_${attribute.attribute_id}">${config.max_file_count}</span> files, 
                            kích thước <span id="maxFileSizeDisplay_${attribute.attribute_id}">${Math.round(config.max_file_size_kb / 1024 * 100) / 100} MB</span> mỗi file
                        </p>
                        <p class="file-upload-extensions">
                            Loại file: <span id="allowedExtensionsDisplay_${attribute.attribute_id}">${config.allowed_extensions.split(',').map(ext => ext.trim().toUpperCase()).join(', ')}</span>
                        </p>
                    </div>
                </div>
                <input type="file" id="fileInput_${attribute.attribute_id}" multiple accept="${config.allowed_extensions.split(',').map(ext => `.${ext.trim()}`).join(',')}" style="display: none;">
            </div>
            
            <!-- File Preview List -->
            <div id="filePreviewList_${attribute.attribute_id}" class="file-preview-list hidden">
                <div class="file-preview-header">
                    <h4 class="file-preview-title">Files đã chọn</h4>
                    <button type="button" id="clearAllFiles_${attribute.attribute_id}" class="clear-all-btn">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Xóa tất cả
                    </button>
                </div>
                <div id="fileList_${attribute.attribute_id}" class="file-list">
                    <!-- Files will be added here dynamically -->
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = fileUploadHtml;
    
    // Initialize file upload manager for this attribute
    initializeFileUploadForAttribute(attribute.attribute_id, config);
}

// Initialize file upload for specific attribute
function initializeFileUploadForAttribute(attributeId, config) {
    const uploadArea = document.getElementById(`fileUploadArea_${attributeId}`);
    const fileInput = document.getElementById(`fileInput_${attributeId}`);
    const clearAllBtn = document.getElementById(`clearAllFiles_${attributeId}`);
    
    let files = [];
    const maxFiles = parseInt(config.max_file_count || 3);
    const maxSize = parseInt(config.max_file_size_kb || 1024);
    const allowedExtensions = (config.allowed_extensions || 'jpg,png,pdf').split(',').map(ext => ext.trim().toLowerCase());
    
    // Click to select files
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    
    // Clear all files
    clearAllBtn.addEventListener('click', () => {
        files = [];
        updateFileList();
    });
    
    function handleFiles(fileList) {
        const newFiles = Array.from(fileList);
        const validFiles = [];
        
        newFiles.forEach(file => {
            if (validateFile(file)) {
                validFiles.push(file);
            }
        });
        
        // Check if adding these files would exceed max count
        if (files.length + validFiles.length > maxFiles) {
            alert(`Chỉ được upload tối đa ${maxFiles} files`);
            return;
        }
        
        files.push(...validFiles);
        updateFileList();
    }
    
    function validateFile(file) {
        // Check file extension
        const extension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(extension)) {
            alert(`File ${file.name} có extension không được phép. Chỉ chấp nhận: ${allowedExtensions.join(', ')}`);
            return false;
        }
        
        // Check file size
        const fileSizeKB = file.size / 1024;
        if (fileSizeKB > maxSize) {
            alert(`File ${file.name} quá lớn. Kích thước tối đa: ${maxSize} KB`);
            return false;
        }
        
        return true;
    }
    
    function removeFile(index) {
        files.splice(index, 1);
        updateFileList();
    }
    
    function updateFileList() {
        const fileList = document.getElementById(`fileList_${attributeId}`);
        const previewList = document.getElementById(`filePreviewList_${attributeId}`);
        
        if (files.length === 0) {
            previewList.classList.add('hidden');
            return;
        }
        
        previewList.classList.remove('hidden');
        
        fileList.innerHTML = files.map((file, index) => `
            <div class="file-item">
                <div class="file-info">
                    <svg class="file-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="file-details">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${formatFileSize(file.size)}</div>
                    </div>
                </div>
                <div class="file-actions">
                    <button type="button" onclick="removeFile(${index})" class="remove-file-btn">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Make removeFile function globally accessible
    window.removeFile = removeFile;
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
