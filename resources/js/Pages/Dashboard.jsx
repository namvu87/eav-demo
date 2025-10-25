import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    Cog6ToothIcon,
    PlusIcon,
    ViewColumnsIcon,
    DocumentTextIcon,
    ChartBarIcon,
    BuildingOfficeIcon,
    WrenchScrewdriverIcon
} from '@heroicons/react/24/outline';

export default function Dashboard({ entityTypes, attributes, entities }) {
    const stats = [
        {
            name: 'Entity Types',
            value: entityTypes?.length || 0,
            icon: BuildingOfficeIcon,
            color: 'bg-blue-500',
            href: route('entity-types.index')
        },
        {
            name: 'Attributes',
            value: attributes?.length || 0,
            icon: WrenchScrewdriverIcon,
            color: 'bg-green-500',
            href: route('attributes.index')
        },
        {
            name: 'Entities',
            value: entities?.length || 0,
            icon: DocumentTextIcon,
            color: 'bg-purple-500',
            href: route('eav.index')
        }
    ];

    const quickActions = [
        {
            name: 'Create Entity Type',
            description: 'Define a new type of entity with its attributes',
            icon: BuildingOfficeIcon,
            href: route('entity-types.create'),
            color: 'bg-blue-500'
        },
        {
            name: 'Create Attribute',
            description: 'Add a new attribute to entity types',
            icon: WrenchScrewdriverIcon,
            href: route('attributes.create'),
            color: 'bg-green-500'
        },
        {
            name: 'Create Entity',
            description: 'Add a new entity instance',
            icon: PlusIcon,
            href: route('eav.create'),
            color: 'bg-purple-500'
        }
    ];

    return (
        <AppLayout title="EAV Dashboard">
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">EAV System Dashboard</h1>
                        <p className="mt-2 text-gray-600">
                            Manage your Entity-Attribute-Value system with dynamic forms and data management
                        </p>
                    </div>

                    {/* Stats */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        {stats.map((stat) => (
                            <Link
                                key={stat.name}
                                href={stat.href}
                                className="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow"
                            >
                                <div className="p-5">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0">
                                            <div className={`p-3 rounded-md ${stat.color}`}>
                                                <stat.icon className="h-6 w-6 text-white" />
                                            </div>
                                        </div>
                                        <div className="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt className="text-sm font-medium text-gray-500 truncate">
                                                    {stat.name}
                                                </dt>
                                                <dd className="text-lg font-medium text-gray-900">
                                                    {stat.value}
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>

                    {/* Quick Actions */}
                    <div className="bg-white shadow rounded-lg mb-8">
                        <div className="px-4 py-5 sm:p-6">
                            <h2 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {quickActions.map((action) => (
                                    <Link
                                        key={action.name}
                                        href={action.href}
                                        className="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all"
                                    >
                                        <div>
                                            <span className={`rounded-lg inline-flex p-3 ${action.color} text-white`}>
                                                <action.icon className="h-6 w-6" />
                                            </span>
                                        </div>
                                        <div className="mt-4">
                                            <h3 className="text-lg font-medium text-gray-900">
                                                {action.name}
                                            </h3>
                                            <p className="mt-2 text-sm text-gray-500">
                                                {action.description}
                                            </p>
                                        </div>
                                        <span className="absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                                            <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4h1a1 1 0 011 1v1a1 1 0 01-1 1h-1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V6H2a1 1 0 01-1-1V5a1 1 0 011-1h1a2 2 0 012-2h12a2 2 0 012 2z" />
                                            </svg>
                                        </span>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Entity Types Management */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {/* Entity Types */}
                        <div className="bg-white shadow rounded-lg">
                            <div className="px-4 py-5 sm:p-6">
                                <div className="flex items-center justify-between mb-4">
                                    <h2 className="text-lg font-medium text-gray-900">Entity Types</h2>
                                    <Link
                                        href={route('entity-types.index')}
                                        className="text-sm text-indigo-600 hover:text-indigo-500"
                                    >
                                        View all
                                    </Link>
                                </div>
                                <p className="text-sm text-gray-500 mb-4">
                                    Define the structure of your entities with custom attributes
                                </p>
                                <div className="space-y-3">
                                    {entityTypes?.slice(0, 5).map((type) => (
                                        <div key={type.entity_type_id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <h3 className="text-sm font-medium text-gray-900">
                                                    {type.type_name}
                                                </h3>
                                                <p className="text-xs text-gray-500">
                                                    {type.type_code} • {type.attributes?.length || 0} attributes
                                                </p>
                                            </div>
                                            <div className="flex space-x-2">
                                                <Link
                                                    href={route('entity-types.manage', type.entity_type_id)}
                                                    className="text-indigo-600 hover:text-indigo-500"
                                                    title="Manage"
                                                >
                                                    <Cog6ToothIcon className="h-4 w-4" />
                                                </Link>
                                                <Link
                                                    href={route('entity-types.show', type.entity_type_id)}
                                                    className="text-gray-600 hover:text-gray-500"
                                                    title="View"
                                                >
                                                    <ViewColumnsIcon className="h-4 w-4" />
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Recent Entities */}
                        <div className="bg-white shadow rounded-lg">
                            <div className="px-4 py-5 sm:p-6">
                                <div className="flex items-center justify-between mb-4">
                                    <h2 className="text-lg font-medium text-gray-900">Recent Entities</h2>
                                    <Link
                                        href={route('eav.index')}
                                        className="text-sm text-indigo-600 hover:text-indigo-500"
                                    >
                                        View all
                                    </Link>
                                </div>
                                <p className="text-sm text-gray-500 mb-4">
                                    Latest entities created in the system
                                </p>
                                <div className="space-y-3">
                                    {entities?.slice(0, 5).map((entity) => (
                                        <div key={entity.entity_id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <h3 className="text-sm font-medium text-gray-900">
                                                    {entity.entity_name}
                                                </h3>
                                                <p className="text-xs text-gray-500">
                                                    {entity.entity_code} • {entity.entity_type?.type_name}
                                                </p>
                                            </div>
                                            <div className="flex space-x-2">
                                                <Link
                                                    href={route('eav.show', entity.entity_id)}
                                                    className="text-indigo-600 hover:text-indigo-500"
                                                    title="View"
                                                >
                                                    <ViewColumnsIcon className="h-4 w-4" />
                                                </Link>
                                                <Link
                                                    href={route('eav.edit', entity.entity_id)}
                                                    className="text-gray-600 hover:text-gray-500"
                                                    title="Edit"
                                                >
                                                    <Cog6ToothIcon className="h-4 w-4" />
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* System Info */}
                    <div className="mt-8 bg-white shadow rounded-lg">
                        <div className="px-4 py-5 sm:p-6">
                            <h2 className="text-lg font-medium text-gray-900 mb-4">System Information</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 className="text-sm font-medium text-gray-900">Features</h3>
                                    <ul className="mt-2 text-sm text-gray-500 space-y-1">
                                        <li>• Dynamic form generation</li>
                                        <li>• Hierarchical entity structure</li>
                                        <li>• Custom attribute types</li>
                                        <li>• Real-time validation</li>
                                        <li>• Search and filtering</li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-gray-900">Benefits</h3>
                                    <ul className="mt-2 text-sm text-gray-500 space-y-1">
                                        <li>• No manual coding for new entity types</li>
                                        <li>• Flexible data structure</li>
                                        <li>• Easy maintenance and updates</li>
                                        <li>• Scalable architecture</li>
                                        <li>• User-friendly interface</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
