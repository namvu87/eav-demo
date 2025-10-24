<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityRelation extends Model
{
    protected $table = 'entity_relations';
    protected $primaryKey = 'relation_id';

    protected $fillable = [
        'source_entity_id',
        'target_entity_id',
        'relation_type',
        'relation_data',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'relation_data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function sourceEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'source_entity_id');
    }

    public function targetEntity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'target_entity_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('relation_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
