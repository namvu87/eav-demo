import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    ArrowLeftIcon, 
    CheckIcon,
    XMarkIcon
} from '@heroicons/react/24/outline';

export default function Create() {
    const { data, setData, post, processing, errors, reset } = useForm({
        type_name: '',
        type_code: '',
        description: '',
        is_active: true
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('entity-types.store'), {
            onSuccess: () => {
                reset();
            }
        });
    };

    return (
        <AppLayout title="Create Entity Type - EAV">
            <div className="min-h-screen bg-gray-50">
                <div className="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div className="bg-white shadow rounded-lg">
                        {/* Header */}
                        <div className="px-4 py-5 sm:p-6 border-b border-gray-200">
                            <div className="flex items-center space-x-4">
                                <Link
                                    href={route('entity-types.index')}
                                    className="text-gray-400 hover:text-gray-600"
                                >
                                    <ArrowLeftIcon className="h-6 w-6" />
                                </Link>
                                <div>
                                    <h1 className="text-2xl font-bold text-gray-900">Create Entity Type</h1>
                                    <p className="mt-1 text-sm text-gray-500">
                                        Define a new type of entity with its attributes
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
                                                Type Name <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.type_name}
                                                onChange={(e) => setData('type_name', e.target.value)}
                                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                                    errors.type_name ? 'border-red-300' : ''
                                                }`}
                                                placeholder="Enter type name"
                                            />
                                            {errors.type_name && (
                                                <p className="text-xs text-red-600 mt-1">{errors.type_name}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Type Code <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.type_code}
                                                onChange={(e) => setData('type_code', e.target.value)}
                                                className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 ${
                                                    errors.type_code ? 'border-red-300' : ''
                                                }`}
                                                placeholder="Enter type code"
                                            />
                                            {errors.type_code && (
                                                <p className="text-xs text-red-600 mt-1">{errors.type_code}</p>
                                            )}
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
                                    </div>
                                </div>

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
                                    href={route('entity-types.index')}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Creating...' : 'Create Entity Type'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
