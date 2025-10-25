import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    PlusIcon, 
    MagnifyingGlassIcon, 
    PencilIcon, 
    TrashIcon,
    EyeIcon,
    Cog6ToothIcon,
    ChevronRightIcon
} from '@heroicons/react/24/outline';

export default function Manage({ entityType, entities }) {
    const [search, setSearch] = useState('');
    const [showFilters, setShowFilters] = useState(false);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('entity-types.manage', entityType.entity_type_id), {
            search
        }, {
            preserveState: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this entity?')) {
            router.delete(route('eav.destroy', id));
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
        <AppLayout title={`Manage ${entityType.type_name} - EAV`}>
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="bg-white shadow rounded-lg mb-6">
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex justify-between items-center">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">
                                        Manage {entityType.type_name}
                                    </h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        {entityType.description || 'Manage entities of this type'}
                                    </p>
                                </div>
                                <div className="flex space-x-3">
                                    <Link
                                        href={route('entity-types.show', entityType.entity_type_id)}
                                        className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        <Cog6ToothIcon className="h-4 w-4 mr-2" />
                                        Configure
                                    </Link>
                                    <Link
                                        href={route('eav.create', { entity_type_id: entityType.entity_type_id })}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PlusIcon className="h-4 w-4 mr-2" />
                                        Add {entityType.type_name}
                                    </Link>
                                </div>
                            </div>

                            {/* Search */}
                            <div className="mt-6">
                                <form onSubmit={handleSearch} className="flex space-x-4">
                                    <div className="flex-1">
                                        <div className="relative">
                                            <MagnifyingGlassIcon className="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                                            <input
                                                type="text"
                                                placeholder={`Search ${entityType.type_name.toLowerCase()}...`}
                                                value={search}
                                                onChange={(e) => setSearch(e.target.value)}
                                                className="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-indigo-500 focus:border-indigo-500"
                                            />
                                        </div>
                                    </div>
                                    <button
                                        type="submit"
                                        className="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                                    >
                                        Search
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {/* Attributes Info */}
                    {entityType.attributes && entityType.attributes.length > 0 && (
                        <div className="bg-white shadow rounded-lg mb-6">
                            <div className="px-4 py-5 sm:p-6">
                                <h2 className="text-lg font-medium text-gray-900 mb-4">
                                    Available Attributes
                                </h2>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {entityType.attributes.map((attribute) => (
                                        <div key={attribute.attribute_id} className="bg-gray-50 rounded-lg p-3">
                                            <div className="flex items-start space-x-2">
                                                <span className="text-lg">
                                                    {getAttributeIcon(attribute.backend_type)}
                                                </span>
                                                <div className="flex-1 min-w-0">
                                                    <h3 className="text-sm font-medium text-gray-900">
                                                        {attribute.attribute_label}
                                                    </h3>
                                                    <p className="text-xs text-gray-500">
                                                        {attribute.attribute_code} • {attribute.backend_type}
                                                    </p>
                                                    {attribute.is_required && (
                                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                                            Required
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Entities Table */}
                    <div className="bg-white shadow rounded-lg">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Entity
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Parent
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {entities.data.map((entity) => (
                                        <tr key={entity.entity_id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {entity.entity_name}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {entity.entity_code}
                                                    </div>
                                                    {entity.description && (
                                                        <div className="text-sm text-gray-400 truncate max-w-xs">
                                                            {entity.description}
                                                        </div>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className="text-sm text-gray-900">
                                                    {entity.parent?.entity_name || 'Root'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                    entity.is_active 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-red-100 text-red-800'
                                                }`}>
                                                    {entity.is_active ? 'Active' : 'Inactive'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {new Date(entity.created_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div className="flex space-x-2">
                                                    <Link
                                                        href={route('eav.show', entity.entity_id)}
                                                        className="text-indigo-600 hover:text-indigo-900"
                                                        title="View"
                                                    >
                                                        <EyeIcon className="h-4 w-4" />
                                                    </Link>
                                                    <Link
                                                        href={route('eav.edit', entity.entity_id)}
                                                        className="text-yellow-600 hover:text-yellow-900"
                                                        title="Edit"
                                                    >
                                                        <PencilIcon className="h-4 w-4" />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(entity.entity_id)}
                                                        className="text-red-600 hover:text-red-900"
                                                        title="Delete"
                                                    >
                                                        <TrashIcon className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {entities.links && (
                            <div className="px-4 py-3 border-t border-gray-200">
                                <div className="flex items-center justify-between">
                                    <div className="text-sm text-gray-700">
                                        Showing {entities.from} to {entities.to} of {entities.total} results
                                    </div>
                                    <div className="flex space-x-1">
                                        {entities.links.map((link, index) => (
                                            <Link
                                                key={index}
                                                href={link.url}
                                                className={`px-3 py-2 text-sm font-medium rounded-md ${
                                                    link.active
                                                        ? 'bg-indigo-600 text-white'
                                                        : 'bg-white text-gray-700 hover:bg-gray-50'
                                                }`}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
