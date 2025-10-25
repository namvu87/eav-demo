import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    PencilIcon, 
    TrashIcon,
    Cog6ToothIcon,
    PlusIcon
} from '@heroicons/react/24/outline';

export default function Show({ entityType }) {
    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this entity type?')) {
            router.delete(route('entity-types.destroy', entityType.entity_type_id));
        }
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
        <AppLayout title={`${entityType.type_name} - Entity Type`}>
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6 border-b border-gray-200">
                            <div className="flex justify-between items-start">
                                <div className="flex items-center space-x-4">
                                    <Link
                                        href={route('entity-types.index')}
                                        className="text-gray-400 hover:text-gray-600"
                                    >
                                        <ArrowLeftIcon className="h-6 w-6" />
                                    </Link>
                                    <div>
                                        <h1 className="text-2xl font-bold text-gray-900">
                                            {entityType.type_name}
                                        </h1>
                                        <div className="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Code: {entityType.type_code}</span>
                                            <span>•</span>
                                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                entityType.is_active 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-red-100 text-red-800'
                                            }`}>
                                                {entityType.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex space-x-3">
                                    <Link
                                        href={route('entity-types.manage', entityType.entity_type_id)}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <Cog6ToothIcon className="h-4 w-4 mr-2" />
                                        Manage
                                    </Link>
                                    <Link
                                        href={route('entity-types.edit', entityType.entity_type_id)}
                                        className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
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

                            {entityType.description && (
                                <div className="mt-4">
                                    <p className="text-gray-700">{entityType.description}</p>
                                </div>
                            )}
                        </div>

                        {/* Entity Type Info */}
                        <div className="px-4 py-5 sm:p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Entity Type ID</h3>
                                    <p className="mt-1 text-sm text-gray-900">{entityType.entity_type_id}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Created</h3>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {new Date(entityType.created_at).toLocaleDateString()}
                                    </p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-500">Updated</h3>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {new Date(entityType.updated_at).toLocaleDateString()}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Attributes */}
                        {entityType.attributes && entityType.attributes.length > 0 && (
                            <div className="px-4 py-5 sm:p-6 border-t border-gray-200">
                                <div className="flex justify-between items-center mb-4">
                                    <h2 className="text-lg font-medium text-gray-900">Attributes</h2>
                                    <Link
                                        href={route('attributes.create', { entity_type_id: entityType.entity_type_id })}
                                        className="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PlusIcon className="h-4 w-4 mr-2" />
                                        Add Attribute
                                    </Link>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {entityType.attributes.map((attribute) => (
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
                                                    <div className="mt-2 flex flex-wrap gap-1">
                                                        {attribute.is_required && (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                Required
                                                            </span>
                                                        )}
                                                        {attribute.is_unique && (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                Unique
                                                            </span>
                                                        )}
                                                        {attribute.is_searchable && (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                Searchable
                                                            </span>
                                                        )}
                                                    </div>
                                                    {attribute.help_text && (
                                                        <p className="text-xs text-gray-400 mt-1">
                                                            {attribute.help_text}
                                                        </p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* No Attributes */}
                        {(!entityType.attributes || entityType.attributes.length === 0) && (
                            <div className="px-4 py-5 sm:p-6 border-t border-gray-200">
                                <div className="text-center py-8">
                                    <div className="text-gray-400 mb-4">
                                        <Cog6ToothIcon className="h-12 w-12 mx-auto" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">No Attributes</h3>
                                    <p className="text-gray-500 mb-4">This entity type doesn't have any attributes yet.</p>
                                    <Link
                                        href={route('attributes.create', { entity_type_id: entityType.entity_type_id })}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PlusIcon className="h-4 w-4 mr-2" />
                                        Add First Attribute
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
