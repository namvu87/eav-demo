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
