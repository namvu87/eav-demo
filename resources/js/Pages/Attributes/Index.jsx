import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    PlusIcon, 
    MagnifyingGlassIcon, 
    PencilIcon, 
    TrashIcon,
    EyeIcon,
    WrenchScrewdriverIcon
} from '@heroicons/react/24/outline';

export default function Index({ attributes, entityTypes, attributeGroups, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [entityTypeFilter, setEntityTypeFilter] = useState(filters.entity_type_id || '');
    const [groupFilter, setGroupFilter] = useState(filters.group_id || '');
    const [showFilters, setShowFilters] = useState(false);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('attributes.index'), {
            search,
            entity_type_id: entityTypeFilter,
            group_id: groupFilter
        }, {
            preserveState: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this attribute?')) {
            router.delete(route('attributes.destroy', id));
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
        <AppLayout title="Attributes - EAV">
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex justify-between items-center">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Attributes</h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Manage attributes for your entity types
                                    </p>
                                </div>
                                <Link
                                    href={route('attributes.create')}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    <PlusIcon className="h-4 w-4 mr-2" />
                                    Add Attribute
                                </Link>
                            </div>

                            {/* Search and Filters */}
                            <div className="mt-6">
                                <form onSubmit={handleSearch} className="flex space-x-4">
                                    <div className="flex-1">
                                        <div className="relative">
                                            <MagnifyingGlassIcon className="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                                            <input
                                                type="text"
                                                placeholder="Search attributes..."
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

                                {/* Advanced Filters */}
                                <div className="mt-4 p-4 bg-gray-50 rounded-md">
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Entity Type
                                            </label>
                                            <select
                                                value={entityTypeFilter}
                                                onChange={(e) => setEntityTypeFilter(e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="">All Types</option>
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
                                                value={groupFilter}
                                                onChange={(e) => setGroupFilter(e.target.value)}
                                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="">All Groups</option>
                                                {attributeGroups.map((group) => (
                                                    <option key={group.group_id} value={group.group_id}>
                                                        {group.group_name}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="flex items-end">
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setSearch('');
                                                    setEntityTypeFilter('');
                                                    setGroupFilter('');
                                                    router.get(route('attributes.index'));
                                                }}
                                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                            >
                                                Clear Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Attributes Table */}
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Attribute
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Entity Type
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Properties
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {attributes.data.map((attribute) => (
                                        <tr key={attribute.attribute_id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <span className="text-2xl mr-3">
                                                        {getAttributeIcon(attribute.backend_type)}
                                                    </span>
                                                    <div>
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {attribute.attribute_label}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {attribute.attribute_code}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className="text-sm text-gray-900">
                                                    {attribute.backend_type}
                                                </span>
                                                <div className="text-xs text-gray-500">
                                                    {attribute.frontend_input}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className="text-sm text-gray-900">
                                                    {attribute.entity_type?.type_name || 'Global'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex flex-wrap gap-1">
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
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div className="flex space-x-2">
                                                    <Link
                                                        href={route('attributes.show', attribute.attribute_id)}
                                                        className="text-indigo-600 hover:text-indigo-900"
                                                        title="View"
                                                    >
                                                        <EyeIcon className="h-4 w-4" />
                                                    </Link>
                                                    <Link
                                                        href={route('attributes.edit', attribute.attribute_id)}
                                                        className="text-yellow-600 hover:text-yellow-900"
                                                        title="Edit"
                                                    >
                                                        <PencilIcon className="h-4 w-4" />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(attribute.attribute_id)}
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
                        {attributes.links && (
                            <div className="px-4 py-3 border-t border-gray-200">
                                <div className="flex items-center justify-between">
                                    <div className="text-sm text-gray-700">
                                        Showing {attributes.from} to {attributes.to} of {attributes.total} results
                                    </div>
                                    <div className="flex space-x-1">
                                        {attributes.links.map((link, index) => (
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
