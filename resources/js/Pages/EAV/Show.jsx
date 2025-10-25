import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    PencilIcon, 
    TrashIcon,
    ChevronRightIcon
} from '@heroicons/react/24/outline';

export default function Show({ entity, attributes }) {
    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this entity?')) {
            router.delete(route('eav.destroy', entity.entity_id));
        }
    };

    const formatValue = (attribute, value) => {
        if (value === null || value === undefined) {
            return '-';
        }

        if (attribute.backend_type === 'file' && typeof value === 'object') {
            return value.name || value.path || '-';
        }

        if (attribute.backend_type === 'datetime') {
            return new Date(value).toLocaleString();
        }

        if (attribute.frontend_input === 'yesno') {
            return value ? 'Yes' : 'No';
        }

        return value;
    };

    const getAttributeIcon = (backendType) => {
        switch (backendType) {
            case 'varchar':
            case 'text':
                return '📝';
            case 'int':
            case 'decimal':
                return '🔢';
            case 'datetime':
                return '📅';
            case 'file':
                return '📎';
            default:
                return '📋';
        }
    };

    return (
        <AppLayout title={`${entity.entity_name} - EAV Entity`}>
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6 border-b border-gray-200">
                            <div className="flex justify-between items-start">
                                <div className="flex items-center space-x-4">
                                    <Link
                                        href={route('eav.index')}
                                        className="text-gray-400 hover:text-gray-600"
                                    >
                                        <ArrowLeftIcon className="h-6 w-6" />
                                    </Link>
                                    <div>
                                        <h1 className="text-2xl font-bold text-gray-900">
                                            {entity.entity_name}
                                        </h1>
                                        <div className="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Code: {entity.entity_code}</span>
                                            <span>•</span>
                                            <span>Type: {entity.entity_type?.type_name || 'N/A'}</span>
                                            <span>•</span>
                                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                entity.is_active 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-red-100 text-red-800'
                                            }`}>
                                                {entity.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex space-x-3">
                                    <Link
                                        href={route('eav.edit', entity.entity_id)}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PencilIcon className="h-4 w-4 mr-2" />
                                        Edit
                                    </Link>
                                    <button
                                        onClick={handleDelete}
                                        className="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                                    >
                                        <TrashIcon className="h-4 w-4 mr-2" />
                                        Delete
                                    </button>
                                </div>
                            </div>

                            {entity.description && (
                                <div className="mt-4">
                                    <p className="text-gray-700">{entity.description}</p>
                                </div>
                            )}
                        </div>

                        {/* Entity Info */}
                        <div className="px-4 py-5 sm:p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Entity ID</h3>
                                    <p className="mt-1 text-sm text-gray-900">{entity.entity_id}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Parent Entity</h3>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {entity.parent?.entity_name || 'Root'}
                                    </p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Level</h3>
                                    <p className="mt-1 text-sm text-gray-900">{entity.level}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Sort Order</h3>
                                    <p className="mt-1 text-sm text-gray-900">{entity.sort_order}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Created</h3>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {new Date(entity.created_at).toLocaleDateString()}
                                    </p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Updated</h3>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {new Date(entity.updated_at).toLocaleDateString()}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Attributes */}
                        {attributes && attributes.length > 0 && (
                            <div className="px-4 py-5 sm:p-6 border-t border-gray-200">
                                <h2 className="text-lg font-medium text-gray-900 mb-4">Attributes</h2>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {attributes.map((attrData) => {
                                        const { attribute, value, display_value } = attrData;
                                        return (
                                            <div key={attribute.attribute_id} className="bg-gray-50 rounded-lg p-4">
                                                <div className="flex items-start space-x-3">
                                                    <span className="text-2xl">
                                                        {getAttributeIcon(attribute.backend_type)}
                                                    </span>
                                                    <div className="flex-1 min-w-0">
                                                        <h3 className="text-sm font-medium text-gray-900">
                                                            {attribute.attribute_label}
                                                        </h3>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            {attribute.attribute_code} • {attribute.backend_type}
                                                        </p>
                                                        <div className="mt-2">
                                                            <p className="text-sm text-gray-900">
                                                                {formatValue(attribute, value)}
                                                            </p>
                                                        </div>
                                                        {attribute.help_text && (
                                                            <p className="text-xs text-gray-400 mt-1">
                                                                {attribute.help_text}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}

                        {/* Children Entities */}
                        {entity.children && entity.children.length > 0 && (
                            <div className="px-4 py-5 sm:p-6 border-t border-gray-200">
                                <h2 className="text-lg font-medium text-gray-900 mb-4">Child Entities</h2>
                                <div className="space-y-2">
                                    {entity.children.map((child) => (
                                        <Link
                                            key={child.entity_id}
                                            href={route('eav.show', child.entity_id)}
                                            className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
                                        >
                                            <div>
                                                <h3 className="text-sm font-medium text-gray-900">
                                                    {child.entity_name}
                                                </h3>
                                                <p className="text-xs text-gray-500">
                                                    {child.entity_code}
                                                </p>
                                            </div>
                                            <ChevronRightIcon className="h-4 w-4 text-gray-400" />
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
