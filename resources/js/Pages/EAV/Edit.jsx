import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    CheckIcon,
    XMarkIcon
} from '@heroicons/react/24/outline';

export default function Edit({ entity, attributes, attributeValues }) {
    const { data, setData, put, processing, errors, reset } = useForm({
        entity_code: entity.entity_code || '',
        entity_name: entity.entity_name || '',
        parent_id: entity.parent_id || null,
        description: entity.description || '',
        is_active: entity.is_active || true,
        sort_order: entity.sort_order || 0,
        ...attributeValues
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('eav.update', entity.entity_id), {
            onSuccess: () => {
                // Form will be reset on success
            }
        });
    };

    const handleInputChange = (field, value) => {
        setData(field, value);
    };

    const renderAttributeField = (attribute) => {
        const fieldName = `attr_${attribute.attribute_id}`;
        const value = data[fieldName] || '';

        switch (attribute.frontend_input) {
            case 'text':
            case 'textarea':
                return (
                    <div key={attribute.attribute_id} className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                            {attribute.attribute_label}
                            {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                        {attribute.frontend_input === 'textarea' ? (
                            <textarea
                                name={fieldName}
                                value={value}
                                onChange={(e) => handleInputChange(fieldName, e.target.value)}
                                placeholder={attribute.placeholder}
                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                    errors[fieldName] ? 'border-red-300' : ''
                                }`}
                                rows={3}
                            />
                        ) : (
                            <input
                                type="text"
                                name={fieldName}
                                value={value}
                                onChange={(e) => handleInputChange(fieldName, e.target.value)}
                                placeholder={attribute.placeholder}
                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                    errors[fieldName] ? 'border-red-300' : ''
                                }`}
                            />
                        )}
                        {attribute.help_text && (
                            <p className="text-xs text-gray-500">{attribute.help_text}</p>
                        )}
                        {errors[fieldName] && (
                            <p className="text-xs text-red-600">{errors[fieldName]}</p>
                        )}
                    </div>
                );

            case 'select':
                return (
                    <div key={attribute.attribute_id} className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                            {attribute.attribute_label}
                            {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                        <select
                            name={fieldName}
                            value={value}
                            onChange={(e) => handleInputChange(fieldName, e.target.value)}
                            className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                errors[fieldName] ? 'border-red-300' : ''
                            }`}
                        >
                            <option value="">Select {attribute.attribute_label}</option>
                            {attribute.options?.map(option => (
                                <option key={option.option_id} value={option.option_id}>
                                    {option.value}
                                </option>
                            ))}
                        </select>
                        {attribute.help_text && (
                            <p className="text-xs text-gray-500">{attribute.help_text}</p>
                        )}
                        {errors[fieldName] && (
                            <p className="text-xs text-red-600">{errors[fieldName]}</p>
                        )}
                    </div>
                );

            case 'multiselect':
                return (
                    <div key={attribute.attribute_id} className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                            {attribute.attribute_label}
                            {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                        <div className="space-y-2">
                            {attribute.options?.map(option => (
                                <label key={option.option_id} className="flex items-center">
                                    <input
                                        type="checkbox"
                                        value={option.option_id}
                                        checked={Array.isArray(value) && value.includes(option.option_id)}
                                        onChange={(e) => {
                                            const currentValues = Array.isArray(value) ? value : [];
                                            const newValues = e.target.checked
                                                ? [...currentValues, option.option_id]
                                                : currentValues.filter(v => v !== option.option_id);
                                            handleInputChange(fieldName, newValues);
                                        }}
                                        className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span className="ml-2 text-sm text-gray-700">{option.value}</span>
                                </label>
                            ))}
                        </div>
                        {attribute.help_text && (
                            <p className="text-xs text-gray-500">{attribute.help_text}</p>
                        )}
                        {errors[fieldName] && (
                            <p className="text-xs text-red-600">{errors[fieldName]}</p>
                        )}
                    </div>
                );

            case 'yesno':
                return (
                    <div key={attribute.attribute_id} className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                            {attribute.attribute_label}
                            {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                        <div className="flex space-x-4">
                            <label className="flex items-center">
                                <input
                                    type="radio"
                                    name={fieldName}
                                    value="1"
                                    checked={value === '1' || value === 1 || value === true}
                                    onChange={(e) => handleInputChange(fieldName, e.target.value)}
                                    className="text-indigo-600 focus:ring-indigo-500"
                                />
                                <span className="ml-2 text-sm text-gray-700">Yes</span>
                            </label>
                            <label className="flex items-center">
                                <input
                                    type="radio"
                                    name={fieldName}
                                    value="0"
                                    checked={value === '0' || value === 0 || value === false}
                                    onChange={(e) => handleInputChange(fieldName, e.target.value)}
                                    className="text-indigo-600 focus:ring-indigo-500"
                                />
                                <span className="ml-2 text-sm text-gray-700">No</span>
                            </label>
                        </div>
                        {attribute.help_text && (
                            <p className="text-xs text-gray-500">{attribute.help_text}</p>
                        )}
                        {errors[fieldName] && (
                            <p className="text-xs text-red-600">{errors[fieldName]}</p>
                        )}
                    </div>
                );

            case 'file':
                return (
                    <div key={attribute.attribute_id} className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                            {attribute.attribute_label}
                            {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                        <input
                            type="file"
                            name={fieldName}
                            onChange={(e) => handleInputChange(fieldName, e.target.files[0])}
                            className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                errors[fieldName] ? 'border-red-300' : ''
                            }`}
                            multiple={attribute.max_file_count > 1}
                        />
                        {value && typeof value === 'object' && value.name && (
                            <p className="text-xs text-gray-500 mt-1">
                                Current file: {value.name}
                            </p>
                        )}
                        {attribute.allowed_extensions && (
                            <p className="text-xs text-gray-500">
                                Allowed extensions: {attribute.allowed_extensions}
                            </p>
                        )}
                        {attribute.max_file_size_kb && (
                            <p className="text-xs text-gray-500">
                                Max file size: {attribute.max_file_size_kb} KB
                            </p>
                        )}
                        {attribute.help_text && (
                            <p className="text-xs text-gray-500">{attribute.help_text}</p>
                        )}
                        {errors[fieldName] && (
                            <p className="text-xs text-red-600">{errors[fieldName]}</p>
                        )}
                    </div>
                );

            default:
                return (
                    <div key={attribute.attribute_id} className="space-y-2">
                        <label className="block text-sm font-medium text-gray-700">
                            {attribute.attribute_label}
                            {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                        <input
                            type={attribute.frontend_input === 'number' ? 'number' : 'text'}
                            name={fieldName}
                            value={value}
                            onChange={(e) => handleInputChange(fieldName, e.target.value)}
                            placeholder={attribute.placeholder}
                            className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                errors[fieldName] ? 'border-red-300' : ''
                            }`}
                        />
                        {attribute.help_text && (
                            <p className="text-xs text-gray-500">{attribute.help_text}</p>
                        )}
                        {errors[fieldName] && (
                            <p className="text-xs text-red-600">{errors[fieldName]}</p>
                        )}
                    </div>
                );
        }
    };

    return (
        <AppLayout title={`Edit ${entity.entity_name} - EAV`}>
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6 border-b border-gray-200">
                            <div className="flex items-center space-x-4">
                                <Link
                                    href={route('eav.show', entity.entity_id)}
                                    className="text-gray-400 hover:text-gray-600"
                                >
                                    <ArrowLeftIcon className="h-6 w-6" />
                                </Link>
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">
                                        Edit {entity.entity_name}
                                    </h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Update entity information and attributes
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
                                            <input
                                                type="text"
                                                value={entity.entity_type?.type_name || 'N/A'}
                                                disabled
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Entity Code <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.entity_code}
                                                onChange={(e) => setData('entity_code', e.target.value)}
                                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                                    errors.entity_code ? 'border-red-300' : ''
                                                }`}
                                                placeholder="Enter entity code"
                                            />
                                            {errors.entity_code && (
                                                <p className="text-xs text-red-600 mt-1">{errors.entity_code}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Entity Name <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.entity_name}
                                                onChange={(e) => setData('entity_name', e.target.value)}
                                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                                    errors.entity_name ? 'border-red-300' : ''
                                                }`}
                                                placeholder="Enter entity name"
                                            />
                                            {errors.entity_name && (
                                                <p className="text-xs text-red-600 mt-1">{errors.entity_name}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Parent Entity
                                            </label>
                                            <input
                                                type="text"
                                                value={entity.parent?.entity_name || 'Root'}
                                                disabled
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                            />
                                        </div>

                                        <div className="md:col-span-2">
                                            <label className="block text-sm font-medium text-gray-700">
                                                Description
                                            </label>
                                            <textarea
                                                value={data.description}
                                                onChange={(e) => setData('description', e.target.value)}
                                                rows={3}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Enter description"
                                            />
                                        </div>

                                        <div className="flex items-center space-x-4">
                                            <label className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={data.is_active}
                                                    onChange={(e) => setData('is_active', e.target.checked)}
                                                    className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                />
                                                <span className="ml-2 text-sm text-gray-700">Active</span>
                                            </label>
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
                                    </div>
                                </div>

                                {/* Dynamic Attributes */}
                                {attributes && attributes.length > 0 && (
                                    <div className="bg-blue-50 rounded-lg p-4">
                                        <h2 className="text-lg font-medium text-gray-900 mb-4">
                                            Attributes for {entity.entity_type?.type_name}
                                        </h2>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {attributes.map(renderAttributeField)}
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
                                    href={route('eav.show', entity.entity_id)}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Updating...' : 'Update Entity'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
