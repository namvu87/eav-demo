<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    protected $table = 'entities';
    protected $primaryKey = 'entity_id';

    protected $fillable = [
        'entity_type_id',
        'entity_code',
        'entity_name',
        'parent_id',
        'path',
        'level',
        'description',
        'metadata',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class, 'entity_type_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Entity::class, 'parent_id');
    }

    // Value relationships
    public function valuesVarchar(): HasMany
    {
        return $this->hasMany(EntityValueVarchar::class, 'entity_id');
    }

    public function valuesText(): HasMany
    {
        return $this->hasMany(EntityValueText::class, 'entity_id');
    }

    public function valuesInt(): HasMany
    {
        return $this->hasMany(EntityValueInt::class, 'entity_id');
    }

    public function valuesDecimal(): HasMany
    {
        return $this->hasMany(EntityValueDecimal::class, 'entity_id');
    }

    public function valuesDatetime(): HasMany
    {
        return $this->hasMany(EntityValueDatetime::class, 'entity_id');
    }

    public function valuesFile(): HasMany
    {
        return $this->hasMany(EntityValueFile::class, 'entity_id');
    }

    // Relations
    public function outgoingRelations(): HasMany
    {
        return $this->hasMany(EntityRelation::class, 'source_entity_id');
    }

    public function incomingRelations(): HasMany
    {
        return $this->hasMany(EntityRelation::class, 'target_entity_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOfType($query, $typeId)
    {
        return $query->where('entity_type_id', $typeId);
    }

    // Helper methods
    public function getEavAttributeValue(string $attributeCode)
    {
        // Load relationship nếu chưa có
        if (!$this->relationLoaded('entityType')) {
            $this->load('entityType');
        }

        // Kiểm tra entityType có tồn tại
        if (!$this->entityType) {
            return null;
        }

        $attribute = $this->entityType->attributes()
            ->where('attribute_code', $attributeCode)
            ->first();

        if (!$attribute) {
            return null;
        }

        $valueModel = $this->getValueModel($attribute->backend_type);

        return $valueModel::where('entity_id', $this->entity_id)
            ->where('attribute_id', $attribute->attribute_id)
            ->first()?->value;
    }

    public function setEavAttributeValue(string $attributeCode, $value)
    {
        $attribute = $this->entityType->attributes()
            ->where('attribute_code', $attributeCode)
            ->first();

        if (!$attribute) {
            return false;
        }

        $valueModel = $this->getValueModel($attribute->backend_type);

        $valueModel::updateOrCreate(
            [
                'entity_id' => $this->entity_id,
                'attribute_id' => $attribute->attribute_id
            ],
            ['value' => $value]
        );

        return true;
    }

    private function getValueModel(string $backendType): string
    {
        return match($backendType) {
            'varchar' => EntityValueVarchar::class,
            'text' => EntityValueText::class,
            'int' => EntityValueInt::class,
            'decimal' => EntityValueDecimal::class,
            'datetime' => EntityValueDatetime::class,
            'file' => EntityValueFile::class,
        };
    }

    // Tree helpers
    public function getAncestors()
    {
        if (!$this->path) {
            return collect([]);
        }

        $ids = array_filter(explode('/', trim($this->path, '/')));

        return Entity::whereIn('entity_id', $ids)
            ->orderBy('level')
            ->get();
    }

    public function getDescendants()
    {
        return Entity::where('path', 'like', $this->path . '%')
            ->where('entity_id', '!=', $this->entity_id)
            ->get();
    }
}
