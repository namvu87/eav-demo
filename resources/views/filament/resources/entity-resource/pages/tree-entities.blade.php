<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        @if(!empty($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Entities</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['total_entities'] ?? 0 }}
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Root Entities</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['root_entities'] ?? 0 }}
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Max Level</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['max_level'] ?? 0 }}
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">Entities by Level</div>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    @foreach($stats['entities_by_level'] ?? [] as $level => $count)
                        <div>Level {{ $level }}: {{ $count }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Type Selector --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <form wire:submit.prevent="loadTreeData">
                {{ $this->form }}
            </form>
        </div>

        {{-- Tree Display --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            @if($treeData->isEmpty())
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <p class="mt-4">No entities found for this type</p>
                </div>
            @else
                <div class="tree-container">
                    @foreach($treeData as $node)
                        @include('filament.resources.entity-resource.pages.tree-node', ['node' => $node, 'level' => 0])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .tree-container {
            font-family: 'Courier New', monospace;
        }
        .tree-node {
            padding: 8px 12px;
            margin: 2px 0;
            border-radius: 6px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .tree-node:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        .tree-node-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tree-icon {
            font-size: 20px;
        }
        .tree-code {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .tree-name {
            flex: 1;
            font-weight: 500;
        }
        .tree-level-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            background-color: rgba(156, 163, 175, 0.2);
            color: #6b7280;
        }
    </style>
</x-filament-panels::page>
