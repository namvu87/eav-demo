<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityValueDecimal extends Model
{
    protected $table = 'entity_values_decimal';
    protected $primaryKey = 'value_id';

    protected $fillable = ['entity_id', 'attribute_id', 'value'];

    protected $casts = ['value' => 'decimal:4'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
