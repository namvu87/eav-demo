@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Entity Hierarchy</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage hierarchical relationships between entities
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('hierarchy.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Root Entity
                        </a>
                    </div>
                </div>

                <!-- Filter -->
                <div class="mt-6">
                    <form method="GET" action="{{ route('hierarchy.index') }}" class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Filter by Entity Type</label>
                            <select name="entity_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Entity Types</option>
                                @foreach($entityTypes as $type)
                                    <option value="{{ $type->entity_type_id }}" {{ request('entity_type_id') == $type->entity_type_id ? 'selected' : '' }}>
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hierarchy Tree -->
            <div class="px-4 py-5 sm:p-6">
                @if($hierarchies->count() > 0)
                    <div class="space-y-4">
                        @foreach($hierarchies as $entity)
                            @include('hierarchy.partials.entity-node', ['entity' => $entity, 'level' => 0])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Entities Found</h3>
                        <p class="text-gray-500 mb-4">Start by creating your first root entity.</p>
                        <a href="{{ route('hierarchy.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Root Entity
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Move Entity Modal -->
<div id="moveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Move Entity</h3>
        <form id="moveForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New Parent</label>
                <select name="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Root Level</option>
                    @foreach($entityTypes as $type)
                        <optgroup label="{{ $type->type_name }}">
                            @foreach($hierarchies as $entity)
                                @if($entity->entity_type_id == $type->entity_type_id)
                                    <option value="{{ $entity->entity_id }}">{{ $entity->entity_name }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeMoveModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    Move Entity
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openMoveModal(entityId) {
    document.getElementById('moveForm').action = `/hierarchy/${entityId}/move`;
    document.getElementById('moveModal').classList.remove('hidden');
}

function closeMoveModal() {
    document.getElementById('moveModal').classList.add('hidden');
}

function toggleChildren(entityId) {
    const children = document.getElementById(`children-${entityId}`);
    const toggle = document.getElementById(`toggle-${entityId}`);
    
    if (children.classList.contains('hidden')) {
        children.classList.remove('hidden');
        toggle.innerHTML = `
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        `;
    } else {
        children.classList.add('hidden');
        toggle.innerHTML = `
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        `;
    }
}
</script>
@endsection
