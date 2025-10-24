<?php

// File: app/Models/AttributeGroup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeGroup extends Model
{
    protected $table = 'attribute_groups';
    protected $primaryKey = 'group_id';
    public $timestamps = false;

    protected $fillable = [
        'entity_type_id',
        'group_code',
        'group_name',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function entityType(): BelongsTo
    {
        return $this->belongsTo(EntityType::class, 'entity_type_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'group_id');
    }
}
