<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use App\Services\EavService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewEntity extends ViewRecord
{
    protected static string $resource = EntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $eavService = app(EavService::class);
        $entityData = $eavService->getEntityWithAttributes($this->record->entity_id);

        $schema = [];

        // Breadcrumb Section (if has ancestors)
        $ancestors = $eavService->getAncestors($this->record);
        if ($ancestors->isNotEmpty()) {
            $breadcrumbString = $eavService->getBreadcrumbString($this->record);
            
            $schema[] = Infolists\Components\Section::make('Hierarchy Path')
                ->schema([
                    Infolists\Components\TextEntry::make('breadcrumb')
                        ->label('Full Path')
                        ->state($breadcrumbString)
                        ->icon('heroicon-m-arrow-right')
                        ->iconColor('primary')
                        ->columnSpanFull()
                        ->size('lg'),
                    
                    Infolists\Components\View::make('filament.resources.entity-resource.pages.ancestors-list')
                        ->viewData([
                            'ancestors' => $ancestors,
                            'current' => $this->record,
                        ])
                        ->columnSpanFull(),
                ])
                ->collapsible();
        }

        // Core Information
        $schema[] = Infolists\Components\Section::make('Core Information')
            ->schema([
                Infolists\Components\TextEntry::make('entity_code')
                    ->label('Code')
                    ->badge()
                    ->color(fn () => $this->record->entityType->color ?? 'gray'),

                Infolists\Components\TextEntry::make('entity_name')
                    ->label('Name')
                    ->weight('bold'),

                Infolists\Components\TextEntry::make('entityType.type_name')
                    ->label('Type')
                    ->badge(),

                Infolists\Components\TextEntry::make('parent.entity_name')
                    ->label('Parent')
                    ->default('-'),

                Infolists\Components\TextEntry::make('level')
                    ->label('Level')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === 0 => 'success',
                        $state <= 2 => 'warning',
                        default => 'danger',
                    }),

                Infolists\Components\TextEntry::make('path')
                    ->label('Path')
                    ->copyable(),

                Infolists\Components\TextEntry::make('description')
                    ->label('Description')
                    ->default('-')
                    ->columnSpanFull(),
            ])
            ->columns(2);

        // Add attribute sections
        if (!empty($entityData['attributes'])) {
            $attributeEntries = [];

            foreach ($entityData['attributes'] as $attrData) {
                $attribute = $attrData['attribute'];
                $displayValue = $attrData['display_value'];

                $attributeEntries[] = Infolists\Components\TextEntry::make('attr_' . $attribute->attribute_id)
                    ->label($attribute->attribute_label)
                    ->default($displayValue)
                    ->helperText($attribute->help_text);
            }

            if (!empty($attributeEntries)) {
                $schema[] = Infolists\Components\Section::make('Attributes')
                    ->schema($attributeEntries)
                    ->columns(2);
            }
        }

        // Tree Children Section
        $children = $eavService->getChildren($this->record);
        if ($children->isNotEmpty()) {
            $schema[] = Infolists\Components\Section::make('Direct Children (' . $children->count() . ')')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('children')
                        ->state($children->map(fn ($child) => [
                            'entity_id' => $child->entity_id,
                            'entity_code' => $child->entity_code,
                            'entity_name' => $child->entity_name,
                            'level' => $child->level,
                        ])->toArray())
                        ->schema([
                            Infolists\Components\TextEntry::make('entity_code')
                                ->badge()
                                ->color('primary'),
                            Infolists\Components\TextEntry::make('entity_name')
                                ->weight('medium'),
                            Infolists\Components\TextEntry::make('level')
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed();
        }

        // Relations section
        $schema[] = Infolists\Components\Section::make('Relations')
            ->schema([
                Infolists\Components\TextEntry::make('outgoing_relations_count')
                    ->label('Outgoing Relations')
                    ->state(fn () => $this->record->outgoingRelations()->count()),

                Infolists\Components\TextEntry::make('incoming_relations_count')
                    ->label('Incoming Relations')
                    ->state(fn () => $this->record->incomingRelations()->count()),
            ])
            ->columns(2)
            ->collapsed();

        return $infolist->schema($schema);
    }
}
