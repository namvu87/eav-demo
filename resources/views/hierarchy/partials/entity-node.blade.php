@php
$indentClass = $level > 0 ? 'ml-' . ($level * 8) : '';
@endphp

<div class="border border-gray-200 rounded-lg p-4 {{ $indentClass }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            @if($entity->children->count() > 0)
                <button 
                    onclick="toggleChildren({{ $entity->entity_id }})"
                    id="toggle-{{ $entity->entity_id }}"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <div class="w-4"></div>
            @endif
            
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $entity->entity_name }}</h3>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $entity->entityType->type_name }}
                    </span>
                    @if($entity->is_active)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Inactive
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">{{ $entity->entity_code }}</p>
                @if($entity->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $entity->description }}</p>
                @endif
                @if($entity->parent)
                    <p class="text-xs text-gray-400 mt-1">Parent: {{ $entity->parent->entity_name }}</p>
                @endif
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">
                Level {{ $entity->level ?? 0 }}
            </span>
            <span class="text-sm text-gray-500">
                {{ $entity->children->count() }} children
            </span>
            
            <div class="flex space-x-1">
                <!-- Add Child Button -->
                <a 
                    href="{{ route('hierarchy.create', ['parent_id' => $entity->entity_id]) }}"
                    class="text-green-600 hover:text-green-500"
                    title="Add Child"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </a>
                
                <!-- View Button -->
                <a 
                    href="{{ route('eav.show', $entity->entity_id) }}"
                    class="text-indigo-600 hover:text-indigo-500"
                    title="View"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
                
                <!-- Edit Button -->
                <a 
                    href="{{ route('eav.edit', $entity->entity_id) }}"
                    class="text-yellow-600 hover:text-yellow-500"
                    title="Edit"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
                
                <!-- Move Button -->
                <button 
                    onclick="openMoveModal({{ $entity->entity_id }})"
                    class="text-blue-600 hover:text-blue-500"
                    title="Move"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </button>
                
                <!-- Delete Button -->
                <form method="POST" action="{{ route('hierarchy.destroy', $entity->entity_id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this entity and all its children?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-500" title="Delete">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Children -->
    @if($entity->children->count() > 0)
        <div id="children-{{ $entity->entity_id }}" class="mt-4 space-y-4 hidden">
            @foreach($entity->children as $child)
                @include('hierarchy.partials.entity-node', ['entity' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
