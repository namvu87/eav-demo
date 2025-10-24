<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityValueFile extends Model
{
    protected $table = 'entity_values_file';
    protected $primaryKey = 'value_id';
    public $timestamps = false;

    protected $fillable = [
        'entity_id',
        'attribute_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_at' => 'datetime'
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
