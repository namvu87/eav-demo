<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeOptionValue extends Model
{
    protected $table = 'attribute_options_value';
    protected $primaryKey = 'value_id';
    public $timestamps = false;

    protected $fillable = [
        'option_id',
        'value'
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'option_id');
    }
}
