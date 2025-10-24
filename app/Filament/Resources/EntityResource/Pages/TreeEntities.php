<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use App\Models\Entity;
use App\Models\EntityType;
use App\Services\EavService;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Collection;

class TreeEntities extends Page
{
    protected static string $resource = EntityResource::class;
    protected static string $view = 'filament.resources.entity-resource.pages.tree-entities';

    public ?int $selectedTypeId = null;
    public Collection $treeData;
    public array $stats = [];

    public function mount(): void
    {
        // Get first active entity type
        $firstType = EntityType::active()->first();
        $this->selectedTypeId = $firstType?->entity_type_id;

        $this->loadTreeData();
    }

    public function loadTreeData(): void
    {
        if (!$this->selectedTypeId) {
            $this->treeData = collect([]);
            $this->stats = [];
            return;
        }

        $eavService = app(EavService::class);
        
        // Load all entities for this type
        $entities = Entity::where('entity_type_id', $this->selectedTypeId)
            ->where('is_active', true)
            ->orderBy('path')
            ->with('entityType')
            ->get();

        $this->treeData = $this->buildTreeArray($entities);
        $this->stats = $eavService->getTreeStats($this->selectedTypeId);
    }

    protected function buildTreeArray(Collection $entities): Collection
    {
        $grouped = $entities->groupBy('parent_id');
        $roots = $grouped->get(null, collect());

        return $roots->map(function ($entity) use ($grouped) {
            return $this->buildNodeArray($entity, $grouped);
        });
    }

    protected function buildNodeArray($entity, $grouped): array
    {
        $children = $grouped->get($entity->entity_id, collect());

        return [
            'entity_id' => $entity->entity_id,
            'entity_code' => $entity->entity_code,
            'entity_name' => $entity->entity_name,
            'level' => $entity->level,
            'path' => $entity->path,
            'icon' => $entity->entityType->icon ?? '📄',
            'color' => $entity->entityType->color ?? '#gray',
            'children' => $children->map(function ($child) use ($grouped) {
                return $this->buildNodeArray($child, $grouped);
            })->toArray(),
        ];
    }

    public function updatedSelectedTypeId(): void
    {
        $this->loadTreeData();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('selectedTypeId')
                ->label('Entity Type')
                ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadTreeData())
                ->searchable(),
        ];
    }

    public function getTitle(): string
    {
        return 'Tree View - Phân cấp cây';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
