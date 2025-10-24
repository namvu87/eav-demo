<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use App\Services\EavService;
use Filament\Resources\Pages\CreateRecord;

class CreateEntity extends CreateRecord
{
    protected static string $resource = EntityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove attr_ fields from core data (will be handled by afterCreate)
        $this->attributeData = [];

        foreach ($data as $key => $value) {
            if (strpos($key, 'attr_') === 0) {
                $this->attributeData[$key] = $value;
                unset($data[$key]);
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->attributeData)) {
            $eavService = app(EavService::class);
            $eavService->saveEntityWithAttributes($this->record, $this->attributeData);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
