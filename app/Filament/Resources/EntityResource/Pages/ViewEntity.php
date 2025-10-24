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

        $schema = [
            Infolists\Components\Section::make('Core Information')
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
                        ->label('Level'),

                    Infolists\Components\TextEntry::make('path')
                        ->label('Path')
                        ->copyable(),

                    Infolists\Components\TextEntry::make('description')
                        ->label('Description')
                        ->default('-')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];

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
