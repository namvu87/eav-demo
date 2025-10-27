@extends('layouts.app')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-6">EAV System Dashboard</h1>
    
    <!-- Stats -->
    <div class="grid grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 mb-2">Entity Types</h3>
            <p class="text-3xl font-bold">{{ $entityTypes->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 mb-2">Attributes</h3>
            <p class="text-3xl font-bold">{{ $attributes->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 mb-2">Entities</h3>
            <p class="text-3xl font-bold">{{ $entities->count() }}</p>
        </div>
    </div>
    
    <!-- Recent Entities -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Entities</h2>
        <div class="space-y-2">
            @forelse($entities as $entity)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium">{{ $entity->entity_name }}</p>
                        <p class="text-sm text-gray-500">{{ $entity->entity_code }}</p>
                    </div>
                    <a href="/eav/{{ $entity->entity_id }}" class="text-blue-600 hover:text-blue-800">View</a>
                </div>
            @empty
                <p class="text-gray-500">No entities yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
