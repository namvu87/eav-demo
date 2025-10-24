<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityValueDatetime extends Model
{
    protected $table = 'entity_values_datetime';
    protected $primaryKey = 'value_id';

    protected $fillable = ['entity_id', 'attribute_id', 'value'];

    protected $casts = ['value' => 'datetime'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
