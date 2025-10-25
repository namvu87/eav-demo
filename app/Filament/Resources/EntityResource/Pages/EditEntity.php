<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use App\Services\EavService;
use App\Models\Attribute;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntity extends EditRecord
{
    protected static string $resource = EntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('move')
                ->label('Move')
                ->icon('heroicon-o-arrows-right-left')
                ->form([
                    \Filament\Forms\Components\Select::make('new_parent_id')
                        ->label('New Parent')
                        ->placeholder('Root (no parent)')
                        ->nullable()
                        ->searchable()
                        ->options(function () {
                            $excludeIds = $this->record->getDescendants()->pluck('entity_id')->push($this->record->entity_id)->all();
                            return \App\Models\Entity::where('entity_type_id', $this->record->entity_type_id)
                                ->whereNotIn('entity_id', $excludeIds)
                                ->orderBy('entity_name')
                                ->pluck('entity_name', 'entity_id');
                        }),
                ])
                ->action(function (array $data) {
                    app(\App\Services\EavService::class)->moveEntity($this->record, $data['new_parent_id'] ?? null);
                    $this->record->refresh();
                })
                ->successNotificationTitle('Entity moved successfully'),
            Actions\Action::make('clone')
                ->label('Clone Entity')
                ->icon('heroicon-o-document-duplicate')
                ->form([
                    \Filament\Forms\Components\TextInput::make('entity_code')
                        ->label('New Code')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('entity_name')
                        ->label('New Name')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $eavService = app(EavService::class);
                    $newEntity = $eavService->cloneEntity(
                        $this->record,
                        $data['entity_code'],
                        $data['entity_name']
                    );

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $newEntity]));
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $entity = $this->record;

        // Load all attribute values
        $valueTypes = ['varchar', 'text', 'int', 'decimal', 'datetime'];

        foreach ($valueTypes as $type) {
            $relation = 'values' . ucfirst($type);
            $values = $entity->$relation()->get();

            foreach ($values as $value) {
                $data['attr_' . $value->attribute_id] = $value->value;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $attributeData = [];
        $coreData = [];

        foreach ($data as $key => $value) {
            if (strpos($key, 'attr_') === 0) {
                $attributeData[$key] = $value;
            } else {
                $coreData[$key] = $value;
            }
        }

        // Save attributes using EavService
        $eavService = app(EavService::class);
        $eavService->saveEntityWithAttributes($this->record, $attributeData);

        return $coreData;
    }

    protected function afterSave(): void
    {
        if (!empty($this->attributeData)) {
            $eavService = app(EavService::class);
            $eavService->saveEntityWithAttributes($this->record, $this->attributeData);
        }
    }

    protected function getValueModel(string $backendType): string
    {
        return match($backendType) {
            'varchar' => \App\Models\EntityValueVarchar::class,
            'text' => \App\Models\EntityValueText::class,
            'int' => \App\Models\EntityValueInt::class,
            'decimal' => \App\Models\EntityValueDecimal::class,
            'datetime' => \App\Models\EntityValueDatetime::class,
            'file' => \App\Models\EntityValueFile::class,
        };
    }
}
