<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Attribute;
use App\Models\EntityValueVarchar;
use App\Models\EntityValueText;
use App\Models\EntityValueInt;
use App\Models\EntityValueDecimal;
use App\Models\EntityValueDatetime;
use App\Models\EntityValueFile;
use Illuminate\Support\Facades\DB;

class EavService
{
    /**
     * Save entity with dynamic attributes
     */
    public function saveEntityWithAttributes(Entity $entity, array $attributeData): Entity
    {
        DB::beginTransaction();

        try {
            // Load original for move detection (only if updating existing entity)
            $original = $entity->exists ? Entity::find($entity->entity_id) : null;

            // Save core entity fields
            $entity->save();

            // If parent changed on update, perform a subtree move
            if ($original && $original->parent_id !== $entity->parent_id) {
                // Use current parent_id as target
                $this->moveEntity($entity, $entity->parent_id);
            } else {
                // Ensure path/level are correct (including new entity creation)
                $this->updateHierarchy($entity);
            }

            // Save attribute values
            $attributes = Attribute::where(function($query) use ($entity) {
                $query->where('entity_type_id', $entity->entity_type_id)
                    ->orWhereNull('entity_type_id');
            })
                ->get()
                ->keyBy('attribute_id');

            foreach ($attributeData as $key => $value) {
                // Extract attribute_id from field name (attr_X)
                if (strpos($key, 'attr_') === 0) {
                    $attributeId = (int) substr($key, 5);

                    if (isset($attributes[$attributeId])) {
                        $attribute = $attributes[$attributeId];
                        $this->saveAttributeValue($entity, $attribute, $value);
                    }
                }
            }

            DB::commit();
            return $entity;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Ensure entity has correct path and level based on current parent.
     * Called after create/update when parent might have changed.
     */
    public function updateHierarchy(Entity $entity): void
    {
        // Reload parent to avoid stale level/path
        $parent = $entity->parent_id ? Entity::find($entity->parent_id) : null;

        if ($parent) {
            $entity->level = ($parent->level ?? 0) + 1;
            // If entity_id is not yet set (not saved), save first
            if (!$entity->entity_id) {
                $entity->save();
            }
            $entity->path = rtrim($parent->path, '/') . '/' . $entity->entity_id . '/';
        } else {
            // Root
            if (!$entity->entity_id) {
                $entity->save();
            }
            $entity->level = 0;
            $entity->path = '/' . $entity->entity_id . '/';
        }

        $entity->save();
    }

    /**
     * Move an entity (and its entire subtree) under a new parent.
     * Pass null as newParentId to move to root.
     */
    public function moveEntity(Entity $entity, ?int $newParentId): Entity
    {
        return DB::transaction(function () use ($entity, $newParentId) {
            // Validate self-move
            if ($newParentId !== null && $newParentId === $entity->entity_id) {
                throw new \InvalidArgumentException('Cannot move entity under itself.');
            }

            // Validate not moving under a descendant
            $descendantIds = $entity->getDescendants()->pluck('entity_id')->all();
            if ($newParentId !== null && in_array($newParentId, $descendantIds, true)) {
                throw new \InvalidArgumentException('Cannot move entity under its own descendant.');
            }

            $oldPath = $entity->path;
            $oldLevel = $entity->level ?? 0;

            // Determine new parent data
            $newParent = $newParentId ? Entity::findOrFail($newParentId) : null;
            $newLevel = $newParent ? ($newParent->level + 1) : 0;
            $newPath = $newParent ? (rtrim($newParent->path, '/') . '/' . $entity->entity_id . '/') : ('/' . $entity->entity_id . '/');

            // Update entity core fields
            $entity->parent_id = $newParentId;
            $entity->level = $newLevel;
            $entity->path = $newPath;
            $entity->save();

            // Update descendants: adjust level delta and replace path prefix
            $levelDelta = $newLevel - $oldLevel;

            // Update levels in bulk
            DB::table('entities')
                ->where('path', 'like', $oldPath . '%')
                ->where('entity_id', '!=', $entity->entity_id)
                ->update([
                    'level' => DB::raw('level + ' . ($levelDelta >= 0 ? $levelDelta : ('(' . $levelDelta . ')'))),
                ]);

            // Replace path prefix using SQL string operations
            // new_path_for_child = CONCAT(?, SUBSTRING(path, LENGTH(?) + 1))
            DB::table('entities')
                ->where('path', 'like', $oldPath . '%')
                ->where('entity_id', '!=', $entity->entity_id)
                ->update([
                    'path' => DB::raw("CONCAT('" . addslashes($newPath) . "', SUBSTRING(path, " . (strlen($oldPath) + 1) . "))"),
                ]);

            return $entity->refresh();
        });
    }

    /**
     * Save single attribute value to appropriate table
     */
    protected function saveAttributeValue(Entity $entity, Attribute $attribute, $value)
    {
        if ($value === null || $value === '') {
            $this->deleteAttributeValue($entity, $attribute);
            return;
        }

        $modelClass = $this->getValueModelClass($attribute->backend_type);

        if ($attribute->backend_type === 'file') {
            // Handle file upload separately
            $this->saveFileValue($entity, $attribute, $value);
            return;
        }

        $modelClass::updateOrCreate(
            [
                'entity_id' => $entity->entity_id,
                'attribute_id' => $attribute->attribute_id
            ],
            ['value' => $value]
        );
    }

    /**
     * Delete attribute value
     */
    protected function deleteAttributeValue(Entity $entity, Attribute $attribute)
    {
        $modelClass = $this->getValueModelClass($attribute->backend_type);

        $modelClass::where('entity_id', $entity->entity_id)
            ->where('attribute_id', $attribute->attribute_id)
            ->delete();
    }

    /**
     * Save file value
     */
    protected function saveFileValue(Entity $entity, Attribute $attribute, $filePath)
    {
        // Implementation depends on your file storage strategy
        // This is a simplified version

        if (is_array($filePath)) {
            $filePath = $filePath[0] ?? null;
        }

        if (!$filePath) {
            return;
        }

        EntityValueFile::create([
            'entity_id' => $entity->entity_id,
            'attribute_id' => $attribute->attribute_id,
            'file_path' => $filePath,
            'file_name' => basename($filePath),
            'file_size' => 0, // Get actual size from storage
            'mime_type' => 'application/octet-stream', // Get actual mime type
        ]);
    }

    /**
     * Get entity with all attribute values
     */
    public function getEntityWithAttributes(int $entityId): array
    {
        $entity = Entity::with('entityType')->findOrFail($entityId);

        $attributes = Attribute::where(function($query) use ($entity) {
            $query->where('entity_type_id', $entity->entity_type_id)
                ->orWhereNull('entity_type_id');
        })
            ->orderBy('sort_order')
            ->get();

        $attributeValues = [];

        foreach ($attributes as $attribute) {
            $value = $this->getAttributeValue($entity, $attribute);

            $attributeValues[] = [
                'attribute' => $attribute,
                'value' => $value,
                'display_value' => $this->formatDisplayValue($attribute, $value),
            ];
        }

        return [
            'entity' => $entity,
            'attributes' => $attributeValues,
        ];
    }

    /**
     * Get single attribute value
     */
    protected function getAttributeValue(Entity $entity, Attribute $attribute)
    {
        $modelClass = $this->getValueModelClass($attribute->backend_type);

        $valueRecord = $modelClass::where('entity_id', $entity->entity_id)
            ->where('attribute_id', $attribute->attribute_id)
            ->first();

        if (!$valueRecord) {
            return null;
        }

        if ($attribute->backend_type === 'file') {
            return [
                'path' => $valueRecord->file_path,
                'name' => $valueRecord->file_name,
                'size' => $valueRecord->file_size,
                'mime' => $valueRecord->mime_type,
            ];
        }

        return $valueRecord->value;
    }

    /**
     * Format value for display
     */
    protected function formatDisplayValue(Attribute $attribute, $value)
    {
        if ($value === null) {
            return '-';
        }

        // For select/multiselect, get option label
        if (in_array($attribute->frontend_input, ['select', 'multiselect']) && is_numeric($value)) {
            $option = $attribute->options()->find($value);
            return $option ? $option->getValue() : $value;
        }

        // For datetime, format
        if ($attribute->backend_type === 'datetime') {
            return $value instanceof \DateTime ?
                $value->format('d/m/Y H:i') :
                \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
        }

        // For yesno, convert to Yes/No
        if ($attribute->frontend_input === 'yesno') {
            return $value ? 'Yes' : 'No';
        }

        // For file, show filename
        if ($attribute->backend_type === 'file' && is_array($value)) {
            return $value['name'] ?? $value['path'] ?? '-';
        }

        return $value;
    }

    /**
     * Get value model class by backend type
     */
    protected function getValueModelClass(string $backendType): string
    {
        return match($backendType) {
            'varchar' => EntityValueVarchar::class,
            'text' => EntityValueText::class,
            'int' => EntityValueInt::class,
            'decimal' => EntityValueDecimal::class,
            'datetime' => EntityValueDatetime::class,
            'file' => EntityValueFile::class,
            default => EntityValueVarchar::class,
        };
    }

    /**
     * Search entities by attribute values
     */
    public function searchEntities(int $entityTypeId, array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Entity::where('entity_type_id', $entityTypeId)
            ->where('is_active', true);

        foreach ($filters as $attributeCode => $searchValue) {
            $attribute = Attribute::where('attribute_code', $attributeCode)
                ->where(function($q) use ($entityTypeId) {
                    $q->where('entity_type_id', $entityTypeId)
                        ->orWhereNull('entity_type_id');
                })
                ->first();

            if (!$attribute) {
                continue;
            }

            $valueTable = 'entity_values_' . $attribute->backend_type;

            $query->whereHas($this->getRelationshipName($attribute->backend_type), function($q) use ($attribute, $searchValue) {
                $q->where('attribute_id', $attribute->attribute_id)
                    ->where('value', 'like', "%{$searchValue}%");
            });
        }

        return $query->get();
    }

    /**
     * Get relationship name by backend type
     */
    protected function getRelationshipName(string $backendType): string
    {
        return 'values' . ucfirst($backendType);
    }

    /**
     * Clone entity with all attributes
     */
    public function cloneEntity(Entity $sourceEntity, string $newCode, string $newName): Entity
    {
        DB::beginTransaction();

        try {
            // Create new entity
            $newEntity = $sourceEntity->replicate();
            $newEntity->entity_code = $newCode;
            $newEntity->entity_name = $newName;
            $newEntity->parent_id = null;
            $newEntity->path = null;
            $newEntity->level = 0;
            $newEntity->save();

            // Update path
            $newEntity->path = '/' . $newEntity->entity_id . '/';
            $newEntity->save();

            // Clone all attribute values
            $this->cloneAttributeValues($sourceEntity, $newEntity);

            DB::commit();
            return $newEntity;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Clone all attribute values
     */
    protected function cloneAttributeValues(Entity $source, Entity $target)
    {
        $valueTypes = ['varchar', 'text', 'int', 'decimal', 'datetime'];

        foreach ($valueTypes as $type) {
            $modelClass = $this->getValueModelClass($type);

            $values = $modelClass::where('entity_id', $source->entity_id)->get();

            foreach ($values as $value) {
                $newValue = $value->replicate();
                $newValue->entity_id = $target->entity_id;
                $newValue->save();
            }
        }
    }

    /**
     * Validate entity attributes
     */
    public function validateEntityAttributes(int $entityTypeId, array $attributeData): array
    {
        $errors = [];

        $attributes = Attribute::where(function($query) use ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId)
                ->orWhereNull('entity_type_id');
        })
            ->where('is_required', true)
            ->get();

        foreach ($attributes as $attribute) {
            $fieldKey = 'attr_' . $attribute->attribute_id;

            if (!isset($attributeData[$fieldKey]) || empty($attributeData[$fieldKey])) {
                $errors[$fieldKey] = $attribute->attribute_label . ' is required.';
            }

            // Check unique constraint
            if ($attribute->is_unique && isset($attributeData[$fieldKey])) {
                $exists = $this->checkUniqueValue(
                    $attribute,
                    $attributeData[$fieldKey],
                    $attributeData['entity_id'] ?? null
                );

                if ($exists) {
                    $errors[$fieldKey] = $attribute->attribute_label . ' must be unique.';
                }
            }
        }

        return $errors;
    }

    /**
     * Check if value is unique
     */
    protected function checkUniqueValue(Attribute $attribute, $value, ?int $excludeEntityId): bool
    {
        $modelClass = $this->getValueModelClass($attribute->backend_type);

        $query = $modelClass::where('attribute_id', $attribute->attribute_id)
            ->where('value', $value);

        if ($excludeEntityId) {
            $query->where('entity_id', '!=', $excludeEntityId);
        }

        return $query->exists();
    }

    /**
     * Get entity hierarchy tree
     */
    public function getEntityHierarchy($entityTypeId = null, $parentId = null)
    {
        $query = Entity::with(['entityType', 'children' => function($query) {
            $query->with(['entityType', 'children'])->orderBy('sort_order')->orderBy('entity_name');
        }])
        ->orderBy('sort_order')
        ->orderBy('entity_name');

        if ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId);
        }

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }


    /**
     * Check if entity is descendant of another entity
     */
    private function isDescendant($entity, $ancestor)
    {
        if ($entity->parent_id === $ancestor->entity_id) {
            return true;
        }
        
        if ($entity->parent_id) {
            $parent = Entity::find($entity->parent_id);
            return $this->isDescendant($parent, $ancestor);
        }
        
        return false;
    }

    /**
     * Get entity path (breadcrumb)
     */
    public function getEntityPath($entityId)
    {
        $entity = Entity::with('entityType')->findOrFail($entityId);
        $path = [$entity];
        
        while ($entity->parent_id) {
            $entity = Entity::with('entityType')->find($entity->parent_id);
            if ($entity) {
                array_unshift($path, $entity);
            } else {
                break;
            }
        }
        
        return $path;
    }
}
