@extends('layouts.app')

@section('content')
<div>
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manage {{ $entityType->type_name }}</h1>
                    <p class="text-sm text-gray-500">{{ $entityType->description ?? 'Manage entities of this type' }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('entity-types.show', $entityType->entity_type_id) }}" class="border border-gray-300 px-4 py-2 rounded hover:bg-gray-50">
                        Configure
                    </a>
                    <a href="{{ route('eav.create', ['entity_type_id' => $entityType->entity_type_id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Add {{ $entityType->type_name }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entities as $entity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $entity->entity_name }}</div>
                            <div class="text-sm text-gray-500">{{ $entity->entity_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $entity->parent->entity_name ?? 'Root' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $entity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $entity->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px窄 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $entity->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('eav.show', $entity->entity_id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('eav.edit', $entity->entity_id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No entities found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entities->hasPages())
        <div class="mt-4">
            {{ $entities->links() }}
        </div>
    @endif
</div>
@endsection

