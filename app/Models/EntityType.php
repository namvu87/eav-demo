<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntityType extends Model
{
    protected $table = 'entity_types';
    protected $primaryKey = 'entity_type_id';

    protected $fillable = [
        'type_code',
        'type_name',
        'type_name_en',
        'icon',
        'color',
        'code_prefix',
        'description',
        'config',
        'is_system',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'config' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'entity_type_id');
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class, 'entity_type_id');
    }

    public function attributeGroups(): HasMany
    {
        return $this->hasMany(AttributeGroup::class, 'entity_type_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotSystem($query)
    {
        return $query->where('is_system', false);
    }
}
