import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    CheckIcon,
    XMarkIcon,
    PlusIcon,
    TrashIcon
} from '@heroicons/react/24/outline';

export default function Create({ entityTypes, attributeGroups, entityTypeId }) {
    const [selectedEntityType, setSelectedEntityType] = useState(entityTypeId || '');
    const [options, setOptions] = useState([]);

    // Mapping backend types to available frontend inputs
    const getAvailableFrontendInputs = (backendType) => {
        const mapping = {
            'varchar': [
                { value: 'text', label: 'Text' },
                { value: 'select', label: 'Select' },
                { value: 'multiselect', label: 'Multi Select' },
                { value: 'yesno', label: 'Yes/No' }
            ],
            'text': [
                { value: 'textarea', label: 'Textarea' }
            ],
            'int': [
                { value: 'number', label: 'Number' },
                { value: 'select', label: 'Select' },
                { value: 'multiselect', label: 'Multi Select' }
            ],
            'decimal': [
                { value: 'number', label: 'Number' }
            ],
            'datetime': [
                { value: 'text', label: 'Text' },
                { value: 'select', label: 'Select' }
            ],
            'file': [
                { value: 'file', label: 'File Upload' }
            ]
        };
        
        return mapping[backendType] || [{ value: 'text', label: 'Text' }];
    };

    const { data, setData, post, processing, errors, reset } = useForm({
        entity_type_id: entityTypeId || '',
        attribute_code: '',
        attribute_label: '',
        backend_type: 'varchar',
        frontend_input: 'text',
        is_required: false,
        is_unique: false,
        is_searchable: false,
        is_filterable: false,
        default_value: '',
        validation_rules: {},
        max_file_count: 1,
        allowed_extensions: '',
        max_file_size_kb: 1024,
        placeholder: '',
        help_text: '',
        frontend_class: '',
        sort_order: 0,
        group_id: '',
        is_system: false,
        is_user_defined: true,
        options: []
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('attributes.store'), {
            onSuccess: () => {
                reset();
            }
        });
    };

    // Handle backend type change
    const handleBackendTypeChange = (backendType) => {
        setData('backend_type', backendType);
        
        // Get available frontend inputs for this backend type
        const availableInputs = getAvailableFrontendInputs(backendType);
        
        // If current frontend_input is not available for this backend type,
        // set it to the first available option
        const currentInput = data.frontend_input;
        const isCurrentInputAvailable = availableInputs.some(input => input.value === currentInput);
        
        if (!isCurrentInputAvailable && availableInputs.length > 0) {
            setData('frontend_input', availableInputs[0].value);
        }
    };

    const addOption = () => {
        const newOptions = [...options, { value: '', label: '', sort_order: options.length }];
        setOptions(newOptions);
        setData('options', newOptions);
    };

    const removeOption = (index) => {
        const newOptions = options.filter((_, i) => i !== index);
        setOptions(newOptions);
        setData('options', newOptions);
    };

    const updateOption = (index, field, value) => {
        const newOptions = [...options];
        newOptions[index][field] = value;
        setOptions(newOptions);
        setData('options', newOptions);
    };

    return (
        <AppLayout title="Create Attribute - EAV">
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6 border-b border-gray-200">
                            <div className="flex items-center space-x-4">
                                <Link
                                    href={route('attributes.index')}
                                    className="text-gray-400 hover:text-gray-600"
                                >
                                    <ArrowLeftIcon className="h-6 w-6" />
                                </Link>
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Create Attribute</h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Add a new attribute to entity types
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Form */}
                        <form onSubmit={handleSubmit} className="px-4 py-5 sm:p-6">
                            <div className="space-y-6">
                                {/* Basic Information */}
                                <div className="bg-gray-50 rounded-lg p-4">
                                    <h2 className="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Entity Type
                                            </label>
                                            <select
                                                value={data.entity_type_id}
                                                onChange={(e) => {
                                                    setData('entity_type_id', e.target.value);
                                                    setSelectedEntityType(e.target.value);
                                                }}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="">Global (All Types)</option>
                                                {entityTypes.map((type) => (
                                                    <option key={type.entity_type_id} value={type.entity_type_id}>
                                                        {type.type_name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Attribute Group
                                            </label>
                                            <select
                                                value={data.group_id}
                                                onChange={(e) => setData('group_id', e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="">No Group</option>
                                                {attributeGroups.map((group) => (
                                                    <option key={group.group_id} value={group.group_id}>
                                                        {group.group_name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Attribute Code <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.attribute_code}
                                                onChange={(e) => setData('attribute_code', e.target.value)}
                                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                                    errors.attribute_code ? 'border-red-300' : ''
                                                }`}
                                                placeholder="Enter attribute code"
                                            />
                                            {errors.attribute_code && (
                                                <p className="text-xs text-red-600 mt-1">{errors.attribute_code}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Attribute Label <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.attribute_label}
                                                onChange={(e) => setData('attribute_label', e.target.value)}
                                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                                    errors.attribute_label ? 'border-red-300' : ''
                                                }`}
                                                placeholder="Enter attribute label"
                                            />
                                            {errors.attribute_label && (
                                                <p className="text-xs text-red-600 mt-1">{errors.attribute_label}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Backend Type <span className="text-red-500">*</span>
                                            </label>
                                            <select
                                                value={data.backend_type}
                                                onChange={(e) => handleBackendTypeChange(e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="varchar">Varchar</option>
                                                <option value="text">Text</option>
                                                <option value="int">Integer</option>
                                                <option value="decimal">Decimal</option>
                                                <option value="datetime">DateTime</option>
                                                <option value="file">File</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Frontend Input <span className="text-red-500">*</span>
                                            </label>
                                            <select
                                                value={data.frontend_input}
                                                onChange={(e) => setData('frontend_input', e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                {getAvailableFrontendInputs(data.backend_type).map((input) => (
                                                    <option key={input.value} value={input.value}>
                                                        {input.label}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        <div className="md:col-span-2">
                                            <label className="block text-sm font-medium text-gray-700">
                                                Default Value
                                            </label>
                                            <input
                                                type="text"
                                                value={data.default_value}
                                                onChange={(e) => setData('default_value', e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Enter default value"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Placeholder
                                            </label>
                                            <input
                                                type="text"
                                                value={data.placeholder}
                                                onChange={(e) => setData('placeholder', e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Enter placeholder text"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Sort Order
                                            </label>
                                            <input
                                                type="number"
                                                value={data.sort_order}
                                                onChange={(e) => setData('sort_order', parseInt(e.target.value) || 0)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                min="0"
                                            />
                                        </div>

                                        <div className="md:col-span-2">
                                            <label className="block text-sm font-medium text-gray-700">
                                                Help Text
                                            </label>
                                            <textarea
                                                value={data.help_text}
                                                onChange={(e) => setData('help_text', e.target.value)}
                                                rows={2}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Enter help text"
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* Properties */}
                                <div className="bg-blue-50 rounded-lg p-4">
                                    <h2 className="text-lg font-medium text-gray-900 mb-4">Properties</h2>
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <label className="flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={data.is_required}
                                                onChange={(e) => setData('is_required', e.target.checked)}
                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            />
                                            <span className="ml-2 text-sm text-gray-700">Required</span>
                                        </label>
                                        <label className="flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={data.is_unique}
                                                onChange={(e) => setData('is_unique', e.target.checked)}
                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            />
                                            <span className="ml-2 text-sm text-gray-700">Unique</span>
                                        </label>
                                        <label className="flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={data.is_searchable}
                                                onChange={(e) => setData('is_searchable', e.target.checked)}
                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            />
                                            <span className="ml-2 text-sm text-gray-700">Searchable</span>
                                        </label>
                                        <label className="flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={data.is_filterable}
                                                onChange={(e) => setData('is_filterable', e.target.checked)}
                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            />
                                            <span className="ml-2 text-sm text-gray-700">Filterable</span>
                                        </label>
                                    </div>
                                </div>

                                {/* Options for Select/MultiSelect */}
                                {(data.frontend_input === 'select' || data.frontend_input === 'multiselect') && (
                                    <div className="bg-green-50 rounded-lg p-4">
                                        <div className="flex justify-between items-center mb-4">
                                            <h2 className="text-lg font-medium text-gray-900">Options</h2>
                                            <button
                                                type="button"
                                                onClick={addOption}
                                                className="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                                            >
                                                <PlusIcon className="h-4 w-4 mr-2" />
                                                Add Option
                                            </button>
                                        </div>
                                        <div className="space-y-3">
                                            {options.map((option, index) => (
                                                <div key={index} className="flex items-center space-x-3">
                                                    <input
                                                        type="text"
                                                        value={option.value}
                                                        onChange={(e) => updateOption(index, 'value', e.target.value)}
                                                        placeholder="Option value"
                                                        className="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    />
                                                    <input
                                                        type="text"
                                                        value={option.label}
                                                        onChange={(e) => updateOption(index, 'label', e.target.value)}
                                                        placeholder="Option label"
                                                        className="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    />
                                                    <input
                                                        type="number"
                                                        value={option.sort_order}
                                                        onChange={(e) => updateOption(index, 'sort_order', parseInt(e.target.value) || 0)}
                                                        placeholder="Order"
                                                        className="w-20 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    />
                                                    <button
                                                        type="button"
                                                        onClick={() => removeOption(index)}
                                                        className="text-red-600 hover:text-red-500"
                                                    >
                                                        <TrashIcon className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* File Settings */}
                                {data.backend_type === 'file' && (
                                    <div className="bg-yellow-50 rounded-lg p-4">
                                        <h2 className="text-lg font-medium text-gray-900 mb-4">File Settings</h2>
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Max File Count
                                                </label>
                                                <input
                                                    type="number"
                                                    value={data.max_file_count}
                                                    onChange={(e) => setData('max_file_count', parseInt(e.target.value) || 1)}
                                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    min="1"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Max File Size (KB)
                                                </label>
                                                <input
                                                    type="number"
                                                    value={data.max_file_size_kb}
                                                    onChange={(e) => setData('max_file_size_kb', parseInt(e.target.value) || 1024)}
                                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    min="1"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Allowed Extensions
                                                </label>
                                                <input
                                                    type="text"
                                                    value={data.allowed_extensions}
                                                    onChange={(e) => setData('allowed_extensions', e.target.value)}
                                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                    placeholder="jpg,png,pdf"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Error Summary */}
                                {Object.keys(errors).length > 0 && (
                                    <div className="bg-red-50 border border-red-200 rounded-md p-4">
                                        <div className="flex">
                                            <XMarkIcon className="h-5 w-5 text-red-400" />
                                            <div className="ml-3">
                                                <h3 className="text-sm font-medium text-red-800">
                                                    Please correct the following errors:
                                                </h3>
                                                <div className="mt-2 text-sm text-red-700">
                                                    <ul className="list-disc list-inside space-y-1">
                                                        {Object.entries(errors).map(([field, error]) => (
                                                            <li key={field}>{error}</li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Form Actions */}
                            <div className="mt-6 flex justify-end space-x-3">
                                <Link
                                    href={route('attributes.index')}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Creating...' : 'Create Attribute'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
