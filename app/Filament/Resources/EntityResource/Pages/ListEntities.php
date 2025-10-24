<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntities extends ListRecords
{
    protected static string $resource = EntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('tree_view')
                ->label('Tree View')
                ->icon('heroicon-o-folder-open')
                ->color('info')
                ->url(fn () => EntityResource::getUrl('tree'))
                ->outlined(),
            Actions\Action::make('expand_all')
                ->label('Show Hierarchy')
                ->icon('heroicon-o-arrows-pointing-out')
                ->color('gray')
                ->action(function () {
                    // This is handled by default sorting by path
                })
                ->outlined(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => ListRecords\Tab::make('All Entities'),
            'roots' => ListRecords\Tab::make('Root Only')
                ->modifyQueryUsing(fn ($query) => $query->whereNull('parent_id'))
                ->badge(fn () => \App\Models\Entity::whereNull('parent_id')->count()),
        ];

        // Add tab for each entity type
        $entityTypes = \App\Models\EntityType::active()
            ->orderBy('sort_order')
            ->get();

        foreach ($entityTypes as $type) {
            $tabs['type_' . $type->entity_type_id] = ListRecords\Tab::make($type->type_name)
                ->modifyQueryUsing(fn ($query) => $query->where('entity_type_id', $type->entity_type_id))
                ->badge(fn () => \App\Models\Entity::where('entity_type_id', $type->entity_type_id)->count())
                ->icon($type->icon);
        }

        return $tabs;
    }
}
