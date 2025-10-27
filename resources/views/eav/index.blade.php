@extends('layouts.app')

@section('content')
<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">EAV Entities</h1>
        <a href="{{ route('eav.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Entity</a>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entities as $entity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $entity->entity_name }}</div>
                            <div class="text-sm text-gray-500">{{ $entity->entity_code }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $entity->entityType->type_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $entity->parent->entity_name ?? 'Root' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $entity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $entity->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('eav.show', $entity->entity_id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('eav.edit', $entity->entity_id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No entities found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
