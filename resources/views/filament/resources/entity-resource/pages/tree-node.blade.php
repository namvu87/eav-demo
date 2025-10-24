<div class="tree-node-wrapper" style="margin-left: {{ $level * 30 }}px;">
    <div class="tree-node" 
         onclick="window.location='{{ route('filament.admin.resources.entities.edit', $node['entity_id']) }}'">
        <div class="tree-node-content">
            {{-- Tree Lines --}}
            @if($level > 0)
                <span class="text-gray-400 dark:text-gray-600">
                    @for($i = 0; $i < $level; $i++)
                        @if($i == $level - 1)
                            └─
                        @else
                            │&nbsp;&nbsp;
                        @endif
                    @endfor
                </span>
            @endif

            {{-- Icon --}}
            <span class="tree-icon">{{ $node['icon'] }}</span>

            {{-- Code Badge --}}
            <span class="tree-code" style="background-color: {{ $node['color'] }}; color: white;">
                {{ $node['entity_code'] }}
            </span>

            {{-- Name --}}
            <span class="tree-name dark:text-white">
                {{ $node['entity_name'] }}
            </span>

            {{-- Level Badge --}}
            <span class="tree-level-badge">
                Level {{ $node['level'] }}
            </span>

            {{-- Children Count --}}
            @if(count($node['children']) > 0)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    ({{ count($node['children']) }} {{ count($node['children']) == 1 ? 'child' : 'children' }})
                </span>
            @endif
        </div>
    </div>

    {{-- Render Children --}}
    @if(count($node['children']) > 0)
        @foreach($node['children'] as $childNode)
            @include('filament.resources.entity-resource.pages.tree-node', ['node' => $childNode, 'level' => $level + 1])
        @endforeach
    @endif
</div>
