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
        // Load attribute values
        $eavService = app(EavService::class);

        $attributes = Attribute::where(function($query) {
            $query->where('entity_type_id', $this->record->entity_type_id)
                ->orWhereNull('entity_type_id');
        })
            ->get();

        foreach ($attributes as $attribute) {
            $fieldKey = 'attr_' . $attribute->attribute_id;
            $valueModel = $this->getValueModel($attribute->backend_type);

            $valueRecord = $valueModel::where('entity_id', $this->record->entity_id)
                ->where('attribute_id', $attribute->attribute_id)
                ->first();

            if ($valueRecord) {
                if ($attribute->backend_type === 'file') {
                    $data[$fieldKey] = [$valueRecord->file_path];
                } else {
                    $data[$fieldKey] = $valueRecord->value;
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract attribute data
        $this->attributeData = [];

        foreach ($data as $key => $value) {
            if (strpos($key, 'attr_') === 0) {
                $this->attributeData[$key] = $value;
                unset($data[$key]);
            }
        }

        return $data;
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
