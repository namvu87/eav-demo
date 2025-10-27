@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">EAV System Dashboard</h1>
            <p class="mt-2 text-gray-600">
                Manage your Entity-Attribute-Value system with dynamic forms and data management
            </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('entity-types.index') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-md bg-blue-500">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Entity Types</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $entityTypes->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('attributes.index') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-md bg-green-500">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Attributes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $attributes->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('eav.index') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-md bg-purple-500">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Entities</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $entities->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('entity-types.create') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium text-gray-900">Create Entity Type</h3>
                            <p class="mt-2 text-sm text-gray-500">Define a new type of entity with its attributes</p>
                        </div>
                        <span class="absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4h1a1 1 0 011 1v1a1 1 0 01-1 1h-1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V6H2a1 1 0 01-1-1V5a1 1 0 011-1h1a2 2 0 012-2h12a2 2 0 012 2z" />
                            </svg>
                        </span>
                    </a>

                    <a href="{{ route('attributes.create') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586l.653-.653a2.548 2.548 0 013.586 0l5.653 4.655" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium text-gray-900">Create Attribute</h3>
                            <p class="mt-2 text-sm text-gray-500">Add a new attribute to entity types</p>
                        </div>
                        <span class="absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4h1a1 1 0 011 1v1a1 1 0 01-1 1h-1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V6H2a1 1 0 01-1-1V5a1 1 0 011-1h1a2 2 0 012-2h12a2 2 0 012 2z" />
                            </svg>
                        </span>
                    </a>

                    <a href="{{ route('eav.create') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-purple-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium text-gray-900">Create Entity</h3>
                            <p class="mt-2 text-sm text-gray-500">Add a new entity instance</p>
                        </div>
                        <span class="absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4h1a1 1 0 011 1v1a1 1 0 01-1 1h-1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V6H2a1 1 0 01-1-1V5a1 1 0 011-1h1a2 2 0 012-2h12a2 2 0 012 2z" />
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Entity Types Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Entity Types -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Entity Types</h2>
                        <a href="{{ route('entity-types.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">
                        Define the structure of your entities with custom attributes
                    </p>
                    <div class="space-y-3">
                        @forelse($entityTypes->take(5) as $type)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $type->type_name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $type->type_code }} • {{ $type->attributes->count() }} attributes</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('entity-types.manage', $type->entity_type_id) }}" class="text-indigo-600 hover:text-indigo-500" title="Manage">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('entity-types.show', $type->entity_type_id) }}" class="text-gray-600 hover:text-gray-500" title="View">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">No entity types found</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Entities -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Recent Entities</h2>
                        <a href="{{ route('eav.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">
                        Latest entities created in the system
                    </p>
                    <div class="space-y-3">
                        @forelse($entities->take(5) as $entity)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $entity->entity_name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $entity->entity_code }} • {{ $entity->entityType->type_name ?? 'N/A' }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('eav.show', $entity->entity_id) }}" class="text-indigo-600 hover:text-indigo-500" title="View">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('eav.edit', $entity->entity_id) }}" class="text-gray-600 hover:text-gray-500" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">No entities found</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">System Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Features</h3>
                        <ul class="mt-2 text-sm text-gray-500 space-y-1">
                            <li>• Dynamic form generation</li>
                            <li>• Hierarchical entity structure</li>
                            <li>• Custom attribute types</li>
                            <li>• Real-time validation</li>
                            <li>• Search and filtering</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Benefits</h3>
                        <ul class="mt-2 text-sm text-gray-500 space-y-1">
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
@endsection
