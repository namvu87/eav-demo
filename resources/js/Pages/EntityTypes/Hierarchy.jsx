import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    PlusIcon,
    PencilIcon,
    TrashIcon,
    EyeIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    BuildingOfficeIcon,
    FolderIcon
} from '@heroicons/react/24/outline';

export default function Hierarchy({ entityTypes, hierarchies }) {
    const [expandedNodes, setExpandedNodes] = useState(new Set());
    const [selectedEntityType, setSelectedEntityType] = useState('');

    const toggleNode = (nodeId) => {
        const newExpanded = new Set(expandedNodes);
        if (newExpanded.has(nodeId)) {
            newExpanded.delete(nodeId);
        } else {
            newExpanded.add(nodeId);
        }
        setExpandedNodes(newExpanded);
    };

    const handleDelete = (entityId) => {
        if (confirm('Are you sure you want to delete this entity and all its children?')) {
            router.delete(route('hierarchy.destroy', entityId));
        }
    };

    const renderTreeNode = (node, level = 0) => {
        const isExpanded = expandedNodes.has(node.entity_id);
        const hasChildren = node.children && node.children.length > 0;
        const indentClass = `ml-${level * 4}`;

        return (
            <div key={node.entity_id} className={`${indentClass} border-l border-gray-200 pl-4`}>
                <div className="flex items-center justify-between py-2 hover:bg-gray-50 rounded">
                    <div className="flex items-center space-x-2">
                        {hasChildren ? (
                            <button
                                onClick={() => toggleNode(node.entity_id)}
                                className="p-1 hover:bg-gray-200 rounded"
                            >
                                {isExpanded ? (
                                    <ChevronDownIcon className="h-4 w-4" />
                                ) : (
                                    <ChevronRightIcon className="h-4 w-4" />
                                )}
                            </button>
                        ) : (
                            <div className="w-6"></div>
                        )}
                        
                        <div className="flex items-center space-x-2">
                            <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                {node.entity_type?.type_code === 'warehouse' ? (
                                    <BuildingOfficeIcon className="h-4 w-4 text-blue-600" />
                                ) : (
                                    <FolderIcon className="h-4 w-4 text-gray-600" />
                                )}
                            </div>
                            <div>
                                <div className="font-medium text-gray-900">{node.entity_name}</div>
                                <div className="text-sm text-gray-500">
                                    {node.entity_code} • {node.entity_type?.type_name}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center space-x-2">
                        <Link
                            href={route('eav.show', node.entity_id)}
                            className="text-indigo-600 hover:text-indigo-500"
                            title="View"
                        >
                            <EyeIcon className="h-4 w-4" />
                        </Link>
                        <Link
                            href={route('eav.edit', node.entity_id)}
                            className="text-yellow-600 hover:text-yellow-500"
                            title="Edit"
                        >
                            <PencilIcon className="h-4 w-4" />
                        </Link>
                        <Link
                            href={route('hierarchy.create', { parent_id: node.entity_id })}
                            className="text-green-600 hover:text-green-500"
                            title="Add Child"
                        >
                            <PlusIcon className="h-4 w-4" />
                        </Link>
                        <button
                            onClick={() => handleDelete(node.entity_id)}
                            className="text-red-600 hover:text-red-500"
                            title="Delete"
                        >
                            <TrashIcon className="h-4 w-4" />
                        </button>
                    </div>
                </div>

                {isExpanded && hasChildren && (
                    <div className="ml-4">
                        {node.children.map(child => renderTreeNode(child, level + 1))}
                    </div>
                )}
            </div>
        );
    };

    return (
        <AppLayout title="Entity Hierarchy - EAV">
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6">
                            <div className="flex justify-between items-center">
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Entity Hierarchy</h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Manage hierarchical entities and their relationships
                                    </p>
                                </div>
                                <div className="flex space-x-3">
                                    <select
                                        value={selectedEntityType}
                                        onChange={(e) => setSelectedEntityType(e.target.value)}
                                        className="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="">All Types</option>
                                        {entityTypes.map((type) => (
                                            <option key={type.entity_type_id} value={type.entity_type_id}>
                                                {type.type_name}
                                            </option>
                                        ))}
                                    </select>
                                    <Link
                                        href={route('hierarchy.create')}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PlusIcon className="h-4 w-4 mr-2" />
                                        Add Root Entity
                                    </Link>
                                </div>
                            </div>
                        </div>

                        {/* Hierarchy Tree */}
                        <div className="px-4 py-5 sm:p-6">
                            {hierarchies.length > 0 ? (
                                <div className="space-y-1">
                                    {hierarchies.map(root => renderTreeNode(root))}
                                </div>
                            ) : (
                                <div className="text-center py-12">
                                    <div className="text-gray-400 mb-4">
                                        <BuildingOfficeIcon className="h-12 w-12 mx-auto" />
                                    </div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-2">No Entities</h3>
                                    <p className="text-gray-500 mb-4">Start by creating your first entity.</p>
                                    <Link
                                        href={route('hierarchy.create')}
                                        className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        <PlusIcon className="h-4 w-4 mr-2" />
                                        Create First Entity
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
