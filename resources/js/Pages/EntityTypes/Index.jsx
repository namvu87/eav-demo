import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    PlusIcon, 
    MagnifyingGlassIcon, 
    PencilIcon, 
    TrashIcon,
    EyeIcon,
    Cog6ToothIcon
} from '@heroicons/react/24/outline';

export default function Index({ entityTypes }) {
    const [search, setSearch] = useState('');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('entity-types.index'), {
            search
        }, {
            preserveState: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this entity type?')) {
            router.delete(route('entity-types.destroy', id));
        }
    };

    return (
        <AppLayout title="Entity Types - EAV">
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex justify-between items-center">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Entity Types</h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Define the structure of your entities with custom attributes
                                    </p>
                                </div>
                                <Link
                                    href={route('entity-types.create')}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    <PlusIcon className="h-4 w-4 mr-2" />
                                    Add Entity Type
                                </Link>
                            </div>

                            {/* Search */}
                            <div className="mt-6">
                                <form onSubmit={handleSearch} className="flex space-x-4">
                                    <div className="flex-1">
                                        <div className="relative">
                                            <MagnifyingGlassIcon className="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                                            <input
                                                type="text"
                                                placeholder="Search entity types..."
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

                        {/* Entity Types Grid */}
                        <div className="px-4 py-5 sm:p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {entityTypes.map((entityType) => (
                                    <div key={entityType.entity_type_id} className="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div className="flex items-start justify-between">
                                            <div className="flex-1">
                                                <h3 className="text-lg font-medium text-gray-900">
                                                    {entityType.type_name}
                                                </h3>
                                                <p className="text-sm text-gray-500 mt-1">
                                                    {entityType.type_code}
                                                </p>
                                                {entityType.description && (
                                                    <p className="text-sm text-gray-600 mt-2">
                                                        {entityType.description}
                                                    </p>
                                                )}
                                                <div className="mt-3 flex items-center space-x-4">
                                                    <span className="text-sm text-gray-500">
                                                        {entityType.attributes?.length || 0} attributes
                                                    </span>
                                                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                        entityType.is_active 
                                                            ? 'bg-green-100 text-green-800' 
                                                            : 'bg-red-100 text-red-800'
                                                    }`}>
                                                        {entityType.is_active ? 'Active' : 'Inactive'}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex space-x-2 ml-4">
                                                <Link
                                                    href={route('entity-types.manage', entityType.entity_type_id)}
                                                    className="text-indigo-600 hover:text-indigo-500"
                                                    title="Manage"
                                                >
                                                    <Cog6ToothIcon className="h-4 w-4" />
                                                </Link>
                                                <Link
                                                    href={route('entity-types.show', entityType.entity_type_id)}
                                                    className="text-gray-600 hover:text-gray-500"
                                                    title="View"
                                                >
                                                    <EyeIcon className="h-4 w-4" />
                                                </Link>
                                                <Link
                                                    href={route('entity-types.edit', entityType.entity_type_id)}
                                                    className="text-yellow-600 hover:text-yellow-500"
                                                    title="Edit"
                                                >
                                                    <PencilIcon className="h-4 w-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(entityType.entity_type_id)}
                                                    className="text-red-600 hover:text-red-500"
                                                    title="Delete"
                                                >
                                                    <TrashIcon className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {entityTypes.length === 0 && (
                                <div className="text-center py-12">
                                    <div className="text-gray-400 mb-4">
                                        <Cog6ToothIcon className="h-12 w-12 mx-auto" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">No Entity Types</h3>
                                    <p className="text-gray-500 mb-4">Get started by creating your first entity type.</p>
                                    <Link
                                        href={route('entity-types.create')}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PlusIcon className="h-4 w-4 mr-2" />
                                        Create Entity Type
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
