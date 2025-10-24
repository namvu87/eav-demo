<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityValueVarchar extends Model
{
    protected $table = 'entity_values_varchar';
    protected $primaryKey = 'value_id';

    protected $fillable = ['entity_id', 'attribute_id', 'value'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
