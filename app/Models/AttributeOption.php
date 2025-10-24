<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeOption extends Model
{
    protected $table = 'attribute_options';
    protected $primaryKey = 'option_id';
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'sort_order',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeOptionValue::class, 'option_id');
    }

    public function getValue(): ?string
    {
        return $this->values()->first()?->value;
    }
}
