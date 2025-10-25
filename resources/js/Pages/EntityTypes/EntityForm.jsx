import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    CheckIcon,
    XMarkIcon,
    PlusIcon,
    CalendarIcon,
    DocumentIcon
} from '@heroicons/react/24/outline';

export default function EntityForm({ entityTypes, entityTypeId, entity = null }) {
    const [selectedEntityType, setSelectedEntityType] = useState(entityTypeId || '');
    const [attributes, setAttributes] = useState([]);
    const [loading, setLoading] = useState(false);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        entity_type_id: entityTypeId || '',
        entity_code: entity?.entity_code || '',
        entity_name: entity?.entity_name || '',
        is_active: entity?.is_active ?? true,
        attributes: entity?.attributes || {}
    });

    // Load attributes when entity type changes
    useEffect(() => {
        if (selectedEntityType) {
            setLoading(true);
            console.log('Loading attributes for entity type:', selectedEntityType);
            fetch(`/api/entity-types/${selectedEntityType}/attributes`)
                .then(response => {
                    console.log('API response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API response data:', data);
                    console.log('Attributes from API:', data.attributes);
                    setAttributes(data.attributes || []);
                    setData('entity_type_id', selectedEntityType);
                    
                    // Initialize attribute values if editing
                    if (entity && entity.attributes) {
                        setData('attributes', entity.attributes);
                    } else {
                        // Initialize empty values for new entity
                        const initialAttributes = {};
                        data.attributes?.forEach(attr => {
                            initialAttributes[attr.attribute_code] = attr.default_value || '';
                        });
                        setData('attributes', initialAttributes);
                    }
                })
                .catch(error => {
                    console.error('Error loading attributes:', error);
                    setAttributes([]);
                })
                .finally(() => {
                    setLoading(false);
                });
        }
    }, [selectedEntityType]);

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (entity) {
            // Update existing entity
            put(route('eav.update', entity.entity_id), {
                onSuccess: () => {
                    // Handle success
                }
            });
        } else {
            // Create new entity
            post(route('eav.store'), {
                onSuccess: () => {
                    reset();
                }
            });
        }
    };

    const handleAttributeChange = (attributeCode, value) => {
        setData('attributes', {
            ...data.attributes,
            [attributeCode]: value
        });
    };

    const renderAttributeField = (attribute) => {
        const value = data.attributes[attribute.attribute_code] || '';
        
        switch (attribute.frontend_input) {
            case 'text':
                return (
                    <input
                        type="text"
                        value={value}
                        onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                        placeholder={attribute.placeholder || `Nhập ${attribute.attribute_label.toLowerCase()}`}
                        className={`w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${
                            errors[`attributes.${attribute.attribute_code}`] ? 'border-red-500' : 'border-gray-300'
                        }`}
                        required={attribute.is_required}
                    />
                );

            case 'textarea':
                return (
                    <textarea
                        value={value}
                        onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                        placeholder={attribute.placeholder || `Nhập ${attribute.attribute_label.toLowerCase()}`}
                        rows={3}
                        className={`w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${
                            errors[`attributes.${attribute.attribute_code}`] ? 'border-red-500' : 'border-gray-300'
                        }`}
                        required={attribute.is_required}
                    />
                );

            case 'number':
                return (
                    <input
                        type="number"
                        value={value}
                        onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                        placeholder={attribute.placeholder || `Nhập ${attribute.attribute_label.toLowerCase()}`}
                        className={`w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${
                            errors[`attributes.${attribute.attribute_code}`] ? 'border-red-500' : 'border-gray-300'
                        }`}
                        required={attribute.is_required}
                    />
                );

            case 'select':
                return (
                    <select
                        value={value}
                        onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                        className={`w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${
                            errors[`attributes.${attribute.attribute_code}`] ? 'border-red-500' : 'border-gray-300'
                        }`}
                        required={attribute.is_required}
                    >
                        <option value="">Chọn {attribute.attribute_label.toLowerCase()}</option>
                        {attribute.options?.map((option) => (
                            <option key={option.option_value} value={option.option_value}>
                                {option.option_label}
                            </option>
                        ))}
                    </select>
                );

            case 'multiselect':
                const selectedValues = Array.isArray(value) ? value : (value ? value.split(',') : []);
                return (
                    <div>
                        <select
                            multiple
                            value={selectedValues}
                            onChange={(e) => {
                                const values = Array.from(e.target.selectedOptions, option => option.value);
                                handleAttributeChange(attribute.attribute_code, values.join(','));
                            }}
                            className={`w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${
                                errors[`attributes.${attribute.attribute_code}`] ? 'border-red-500' : 'border-gray-300'
                            }`}
                            required={attribute.is_required}
                        >
                            {attribute.options?.map((option) => (
                                <option key={option.option_value} value={option.option_value}>
                                    {option.option_label}
                                </option>
                            ))}
                        </select>
                        <p className="text-xs text-gray-500 mt-1">Giữ Ctrl để chọn nhiều</p>
                    </div>
                );

            case 'yesno':
                return (
                    <div className="flex space-x-4">
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name={attribute.attribute_code}
                                value="1"
                                checked={value === '1' || value === true}
                                onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                                className="mr-2"
                            />
                            Có
                        </label>
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name={attribute.attribute_code}
                                value="0"
                                checked={value === '0' || value === false}
                                onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                                className="mr-2"
                            />
                            Không
                        </label>
                    </div>
                );

            case 'file':
                return (
                    <div>
                        <input
                            type="file"
                            onChange={(e) => {
                                const file = e.target.files[0];
                                if (file) {
                                    handleAttributeChange(attribute.attribute_code, file);
                                }
                            }}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            accept={attribute.allowed_extensions ? `.${attribute.allowed_extensions.split(',').join(',.')}` : undefined}
                        />
                        {attribute.max_file_size_kb && (
                            <p className="text-xs text-gray-500 mt-1">
                                Kích thước tối đa: {attribute.max_file_size_kb}KB
                            </p>
                        )}
                    </div>
                );

            default:
                return (
                    <input
                        type="text"
                        value={value}
                        onChange={(e) => handleAttributeChange(attribute.attribute_code, e.target.value)}
                        placeholder={attribute.placeholder || `Nhập ${attribute.attribute_label.toLowerCase()}`}
                        className={`w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 ${
                            errors[`attributes.${attribute.attribute_code}`] ? 'border-red-500' : 'border-gray-300'
                        }`}
                        required={attribute.is_required}
                    />
                );
        }
    };

    const getAttributeIcon = (frontendInput) => {
        switch (frontendInput) {
            case 'text':
                return '📝';
            case 'textarea':
                return '📄';
            case 'number':
                return '🔢';
            case 'select':
            case 'multiselect':
                return '📋';
            case 'yesno':
                return '✅';
            case 'file':
                return '📎';
            default:
                return '📝';
        }
    };

    return (
        <AppLayout title={entity ? "Chỉnh sửa Entity" : "Tạo Entity mới"}>
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6 border-b border-gray-200">
                            <div className="flex items-center space-x-4">
                                <Link
                                    href={route('eav.index')}
                                    className="text-gray-400 hover:text-gray-600"
                                >
                                    <ArrowLeftIcon className="h-6 w-6" />
                                </Link>
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">
                                        {entity ? 'Chỉnh sửa Entity' : 'Tạo Entity mới'}
                                    </h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        {entity ? 'Cập nhật thông tin entity' : 'Nhập thông tin cho entity mới'}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Form */}
                        <form onSubmit={handleSubmit} className="px-4 py-5 sm:p-6">
                            <div className="space-y-6">
                                {/* Basic Information */}
                                <div className="bg-gray-50 rounded-lg p-4">
                                    <h2 className="text-lg font-medium text-gray-900 mb-4">Thông tin cơ bản</h2>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Loại Entity <span className="text-red-500">*</span>
                                            </label>
                                            <select
                                                value={selectedEntityType}
                                                onChange={(e) => setSelectedEntityType(e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                required
                                            >
                                                <option value="">Chọn loại entity</option>
                                                {entityTypes.map((type) => (
                                                    <option key={type.entity_type_id} value={type.entity_type_id}>
                                                        {type.type_name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Mã Entity <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.entity_code}
                                                onChange={(e) => setData('entity_code', e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Nhập mã entity"
                                                required
                                            />
                                        </div>

                                        <div className="md:col-span-2">
                                            <label className="block text-sm font-medium text-gray-700">
                                                Tên Entity <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.entity_name}
                                                onChange={(e) => setData('entity_name', e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Nhập tên entity"
                                                required
                                            />
                                        </div>

                                        <div className="flex items-center">
                                            <label className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={data.is_active}
                                                    onChange={(e) => setData('is_active', e.target.checked)}
                                                    className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                />
                                                <span className="ml-2 text-sm text-gray-700">Kích hoạt</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {/* Attributes Section */}
                                {selectedEntityType && (
                                    <div className="bg-blue-50 rounded-lg p-4">
                                        <div className="flex items-center justify-between mb-4">
                                            <h2 className="text-lg font-medium text-gray-900">Attributes</h2>
                                            {loading && (
                                                <div className="text-sm text-gray-500">Đang tải...</div>
                                            )}
                                        </div>

                                        {loading ? (
                                            <div className="text-center py-8">
                                                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                                                <p className="mt-2 text-sm text-gray-500">Đang tải attributes...</p>
                                            </div>
                                        ) : (() => {
                                            console.log('Rendering attributes section. Attributes count:', attributes.length);
                                            console.log('Attributes data:', attributes);
                                            return attributes.length > 0;
                                        })() ? (
                                            <div className="space-y-4">
                                                {attributes.map((attribute) => (
                                                    <div key={attribute.attribute_id} className="bg-white rounded-lg p-4 border">
                                                        <div className="flex items-start space-x-3">
                                                            <span className="text-2xl">
                                                                {getAttributeIcon(attribute.frontend_input)}
                                                            </span>
                                                            <div className="flex-1">
                                                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                                                    {attribute.attribute_label}
                                                                    {attribute.is_required && (
                                                                        <span className="text-red-500 ml-1">*</span>
                                                                    )}
                                                                </label>
                                                                {renderAttributeField(attribute)}
                                                                {attribute.help_text && (
                                                                    <p className="text-xs text-gray-500 mt-1">
                                                                        {attribute.help_text}
                                                                    </p>
                                                                )}
                                                                {errors[`attributes.${attribute.attribute_code}`] && (
                                                                    <p className="text-xs text-red-600 mt-1">
                                                                        {errors[`attributes.${attribute.attribute_code}`]}
                                                                    </p>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        ) : (
                                            <div className="text-center py-8">
                                                <div className="text-gray-400 mb-4">
                                                    <DocumentIcon className="h-12 w-12 mx-auto" />
                                                </div>
                                                <h3 className="text-lg font-medium text-gray-900 mb-2">Không có attributes</h3>
                                                <p className="text-gray-500 mb-4">
                                                    Entity type này chưa có attributes nào được định nghĩa.
                                                </p>
                                                <Link
                                                    href={route('attributes.create', { entity_type_id: selectedEntityType })}
                                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                                >
                                                    <PlusIcon className="h-4 w-4 mr-2" />
                                                    Thêm Attribute
                                                </Link>
                                            </div>
                                        )}
                                    </div>
                                )}

                                {/* Error Summary */}
                                {Object.keys(errors).length > 0 && (
                                    <div className="bg-red-50 border border-red-200 rounded-md p-4">
                                        <div className="flex">
                                            <XMarkIcon className="h-5 w-5 text-red-400" />
                                            <div className="ml-3">
                                                <h3 className="text-sm font-medium text-red-800">
                                                    Vui lòng sửa các lỗi sau:
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
                                    href={route('eav.index')}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Hủy
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing || loading}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Đang lưu...' : (entity ? 'Cập nhật' : 'Tạo mới')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
