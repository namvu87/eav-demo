<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpParser\Node\AttributeGroup;

class Attribute extends Model
{
    protected $table = 'attributes';
    protected $primaryKey = 'attribute_id';

    protected $fillable = [
        'entity_type_id',
        'attribute_code',
        'attribute_label',
        'backend_type',
        'frontend_input',
        'is_required',
        'is_unique',
        'is_searchable',
        'is_filterable',
        'default_value',
        'validation_rules',
        'max_file_count',
        'allowed_extensions',
        'max_file_size_kb',
        'placeholder',
        'help_text',
        'frontend_class',
        'sort_order',
        'group_id',
        'is_system',
        'is_user_defined'
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_searchable' => 'boolean',
        'is_filterable' => 'boolean',
        'is_system' => 'boolean',
        'is_user_defined' => 'boolean',
        'max_file_count' => 'integer',
        'max_file_size_kb' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class, 'entity_type_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class, 'group_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id');
    }

    // Value relationships (polymorphic pattern)
    public function valuesVarchar(): HasMany
    {
        return $this->hasMany(EntityValueVarchar::class, 'attribute_id');
    }

    public function valuesText(): HasMany
    {
        return $this->hasMany(EntityValueText::class, 'attribute_id');
    }

    public function valuesInt(): HasMany
    {
        return $this->hasMany(EntityValueInt::class, 'attribute_id');
    }

    public function valuesDecimal(): HasMany
    {
        return $this->hasMany(EntityValueDecimal::class, 'attribute_id');
    }

    public function valuesDatetime(): HasMany
    {
        return $this->hasMany(EntityValueDatetime::class, 'attribute_id');
    }

    public function valuesFile(): HasMany
    {
        return $this->hasMany(EntityValueFile::class, 'attribute_id');
    }

    // Scopes
    public function scopeForType($query, $typeId)
    {
        return $query->where('entity_type_id', $typeId)
            ->orWhereNull('entity_type_id');
    }

    public function scopeShared($query)
    {
        return $query->whereNull('entity_type_id');
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    // Helper methods
    public function getValueTableName(): string
    {
        return 'entity_values_' . $this->backend_type;
    }

    public function isFileType(): bool
    {
        return $this->backend_type === 'file';
    }

    public function isSelectType(): bool
    {
        return in_array($this->frontend_input, ['select', 'multiselect']);
    }
}
