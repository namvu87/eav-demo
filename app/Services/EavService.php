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
            // Save core entity fields
            $entity->save();

            // Update path and level if has parent
            if ($entity->parent_id) {
                $parent = Entity::find($entity->parent_id);
                $entity->level = $parent->level + 1;
                $entity->path = $parent->path . $entity->entity_id . '/';
            } else {
                $entity->level = 0;
                $entity->path = '/' . $entity->entity_id . '/';
            }
            $entity->save();

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
     * ==========================================
     * TREE HIERARCHY METHODS
     * ==========================================
     */

    /**
     * Get ancestors (breadcrumb) for an entity
     * Returns: [root, parent, grandparent, ...]
     */
    public function getAncestors(Entity $entity): \Illuminate\Support\Collection
    {
        if (!$entity->path) {
            return collect([]);
        }

        // Extract entity IDs from path: "/1/5/12/" => [1, 5, 12]
        $ids = array_filter(explode('/', trim($entity->path, '/')));

        if (empty($ids)) {
            return collect([]);
        }

        // Get entities in correct order
        return Entity::whereIn('entity_id', $ids)
            ->orderBy('level')
            ->get();
    }

    /**
     * Get all descendants of an entity
     * Returns all children recursively
     */
    public function getDescendants(Entity $entity): \Illuminate\Support\Collection
    {
        if (!$entity->path) {
            return collect([]);
        }

        return Entity::where('path', 'like', $entity->path . '%')
            ->where('entity_id', '!=', $entity->entity_id)
            ->orderBy('path')
            ->get();
    }

    /**
     * Get direct children only
     */
    public function getChildren(Entity $entity): \Illuminate\Support\Collection
    {
        return Entity::where('parent_id', $entity->entity_id)
            ->orderBy('sort_order')
            ->orderBy('entity_name')
            ->get();
    }

    /**
     * Get tree structure for entity type
     * Returns hierarchical tree with nested children
     */
    public function getTree(int $entityTypeId, ?int $rootEntityId = null): \Illuminate\Support\Collection
    {
        $query = Entity::where('entity_type_id', $entityTypeId)
            ->where('is_active', true)
            ->orderBy('path');

        if ($rootEntityId) {
            $rootEntity = Entity::find($rootEntityId);
            if ($rootEntity) {
                $query->where(function($q) use ($rootEntity) {
                    $q->where('entity_id', $rootEntity->entity_id)
                        ->orWhere('path', 'like', $rootEntity->path . '%');
                });
            }
        } else {
            // Only get roots if no specific root requested
            $query->whereNull('parent_id');
        }

        $entities = $query->get();

        return $this->buildTreeStructure($entities);
    }

    /**
     * Build nested tree structure from flat collection
     */
    protected function buildTreeStructure(\Illuminate\Support\Collection $entities): \Illuminate\Support\Collection
    {
        $grouped = $entities->groupBy('parent_id');
        $roots = $grouped->get(null, collect());

        return $roots->map(function ($entity) use ($grouped) {
            return $this->attachChildren($entity, $grouped);
        });
    }

    /**
     * Recursively attach children to entity
     */
    protected function attachChildren($entity, $grouped)
    {
        $children = $grouped->get($entity->entity_id, collect());

        $entity->children_nodes = $children->map(function ($child) use ($grouped) {
            return $this->attachChildren($child, $grouped);
        });

        return $entity;
    }

    /**
     * Move entity to new parent
     * Updates path and level for entity and all descendants
     */
    public function moveEntity(Entity $entity, ?int $newParentId): bool
    {
        DB::beginTransaction();

        try {
            // Validate: cannot move to itself or its descendants
            if ($newParentId) {
                $newParent = Entity::find($newParentId);

                if (!$newParent) {
                    throw new \Exception('New parent not found');
                }

                // Check if new parent is a descendant of current entity
                if ($newParent->path && strpos($newParent->path, "/{$entity->entity_id}/") !== false) {
                    throw new \Exception('Cannot move entity to its own descendant');
                }

                // Check if types are compatible (if needed)
                if ($entity->entity_type_id !== $newParent->entity_type_id) {
                    // Optional: Add logic to check if different types are allowed
                }
            }

            // Store old path for updating descendants
            $oldPath = $entity->path;

            // Update entity parent
            $entity->parent_id = $newParentId;

            if ($newParentId) {
                $parent = Entity::find($newParentId);
                $entity->level = $parent->level + 1;
                $entity->path = $parent->path . $entity->entity_id . '/';
            } else {
                $entity->level = 0;
                $entity->path = '/' . $entity->entity_id . '/';
            }

            $entity->save();

            // Update all descendants paths
            $this->updateDescendantsPaths($entity, $oldPath);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update paths for all descendants after move
     */
    protected function updateDescendantsPaths(Entity $entity, string $oldPath): void
    {
        $descendants = Entity::where('path', 'like', $oldPath . '%')
            ->where('entity_id', '!=', $entity->entity_id)
            ->get();

        foreach ($descendants as $descendant) {
            // Replace old path prefix with new path
            $descendant->path = str_replace($oldPath, $entity->path, $descendant->path);

            // Recalculate level
            $descendant->level = substr_count($descendant->path, '/') - 2;

            $descendant->save();
        }
    }

    /**
     * Get breadcrumb string for entity
     * Format: "Root → Parent → Child"
     */
    public function getBreadcrumbString(Entity $entity, string $separator = ' → '): string
    {
        $ancestors = $this->getAncestors($entity);

        if ($ancestors->isEmpty()) {
            return $entity->entity_name;
        }

        return $ancestors->pluck('entity_name')->implode($separator);
    }

    /**
     * Get tree statistics
     */
    public function getTreeStats(int $entityTypeId): array
    {
        $entities = Entity::where('entity_type_id', $entityTypeId)->get();

        return [
            'total_entities' => $entities->count(),
            'root_entities' => $entities->whereNull('parent_id')->count(),
            'max_level' => $entities->max('level') ?? 0,
            'entities_by_level' => $entities->groupBy('level')->map->count(),
        ];
    }
}
