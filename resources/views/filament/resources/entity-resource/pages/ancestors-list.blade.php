<div class="space-y-2">
    <div class="flex items-center gap-2 text-sm">
        @foreach($ancestors as $index => $ancestor)
            {{-- Ancestor Item --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('filament.admin.resources.entities.view', $ancestor->entity_id) }}" 
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-lg">{{ $ancestor->entityType->icon ?? '📄' }}</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">
                        {{ $ancestor->entity_code }}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        {{ $ancestor->entity_name }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        L{{ $ancestor->level }}
                    </span>
                </a>
                
                {{-- Arrow Separator --}}
                @if($index < count($ancestors) - 1)
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </div>
        @endforeach
        
        {{-- Current Entity (highlighted) --}}
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary-100 dark:bg-primary-900 border-2 border-primary-500">
                <span class="text-lg">{{ $current->entityType->icon ?? '📄' }}</span>
                <span class="font-bold text-primary-700 dark:text-primary-300">
                    {{ $current->entity_code }}
                </span>
                <span class="text-primary-600 dark:text-primary-400">
                    {{ $current->entity_name }}
                </span>
                <span class="text-xs px-2 py-1 rounded-full bg-primary-200 dark:bg-primary-800 text-primary-700 dark:text-primary-300">
                    L{{ $current->level }} (Current)
                </span>
            </div>
        </div>
    </div>
</div>
