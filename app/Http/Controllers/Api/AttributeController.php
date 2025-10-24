<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\AttributeOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_type_id' => ['nullable', 'integer', 'exists:entity_types,entity_type_id'],
            'attribute_code' => ['required', 'string', 'max:100'],
            'attribute_label' => ['required', 'string', 'max:255'],
            'backend_type' => ['required', 'in:varchar,text,int,decimal,datetime,file'],
            'frontend_input' => ['required', 'in:text,textarea,select,multiselect,date,datetime,yesno,file'],
            'is_required' => ['sometimes', 'boolean'],
            'is_unique' => ['sometimes', 'boolean'],
            'is_searchable' => ['sometimes', 'boolean'],
            'is_filterable' => ['sometimes', 'boolean'],
            'default_value' => ['nullable'],
            'validation_rules' => ['nullable', 'array'],
            'max_file_count' => ['nullable', 'integer'],
            'allowed_extensions' => ['nullable', 'string'],
            'max_file_size_kb' => ['nullable', 'integer'],
            'placeholder' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string'],
            'frontend_class' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'group_id' => ['nullable', 'integer', 'exists:attribute_groups,group_id'],
            'options' => ['nullable', 'array'],
            'options.*.label' => ['required_with:options', 'string'],
            'options.*.is_default' => ['boolean'],
        ]);

        // Ensure uniqueness within type or globally if shared
        $exists = Attribute::where('attribute_code', $data['attribute_code'])
            ->when(isset($data['entity_type_id']), function ($q) use ($data) {
                $q->where('entity_type_id', $data['entity_type_id']);
            }, function ($q) {
                $q->whereNull('entity_type_id');
            })
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Attribute code already exists'], 422);
        }

        return DB::transaction(function () use ($data) {
            $attribute = Attribute::create($data);

            if (!empty($data['options']) && in_array($attribute->frontend_input, ['select', 'multiselect'])) {
                foreach ($data['options'] as $i => $opt) {
                    $option = AttributeOption::create([
                        'attribute_id' => $attribute->attribute_id,
                        'sort_order' => $i + 1,
                        'is_default' => (bool)($opt['is_default'] ?? false),
                    ]);
                    AttributeOptionValue::create([
                        'option_id' => $option->option_id,
                        'value' => $opt['label'],
                    ]);
                }
            }

            return response()->json(['success' => true, 'data' => $attribute], 201);
        });
    }

    public function listByType(int $typeId)
    {
        $attributes = Attribute::where(function ($q) use ($typeId) {
            $q->where('entity_type_id', $typeId)->orWhereNull('entity_type_id');
        })->orderBy('sort_order')->get();
        return response()->json($attributes);
    }

    public function listShared()
    {
        return response()->json(Attribute::whereNull('entity_type_id')->get());
    }

    public function show(int $id)
    {
        return response()->json(Attribute::findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $attribute = Attribute::findOrFail($id);
        $data = $request->validate([
            'attribute_code' => ['sometimes', 'string', 'max:100'],
            'attribute_label' => ['sometimes', 'string', 'max:255'],
            'backend_type' => ['sometimes', 'in:varchar,text,int,decimal,datetime,file'],
            'frontend_input' => ['sometimes', 'in:text,textarea,select,multiselect,date,datetime,yesno,file'],
            'is_required' => ['sometimes', 'boolean'],
            'is_unique' => ['sometimes', 'boolean'],
            'is_searchable' => ['sometimes', 'boolean'],
            'is_filterable' => ['sometimes', 'boolean'],
            'default_value' => ['nullable'],
            'validation_rules' => ['nullable', 'array'],
            'max_file_count' => ['nullable', 'integer'],
            'allowed_extensions' => ['nullable', 'string'],
            'max_file_size_kb' => ['nullable', 'integer'],
            'placeholder' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string'],
            'frontend_class' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'group_id' => ['nullable', 'integer', 'exists:attribute_groups,group_id'],
        ]);
        $attribute->update($data);
        return response()->json(['success' => true, 'data' => $attribute]);
    }

    public function destroy(int $id)
    {
        $attribute = Attribute::findOrFail($id);
        if ($attribute->is_system) {
            return response()->json(['success' => false, 'message' => 'Cannot delete system attribute'], 422);
        }
        $attribute->delete();
        return response()->json(['success' => true]);
    }

    public function addOption(Request $request, int $id)
    {
        $attribute = Attribute::findOrFail($id);
        $data = $request->validate([
            'label' => ['required', 'string'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
        $option = AttributeOption::create([
            'attribute_id' => $attribute->attribute_id,
            'sort_order' => ($attribute->options()->max('sort_order') ?? 0) + 1,
            'is_default' => (bool)($data['is_default'] ?? false),
        ]);
        AttributeOptionValue::create([
            'option_id' => $option->option_id,
            'value' => $data['label'],
        ]);
        return response()->json(['success' => true, 'data' => $option->load('values')]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.attribute_id' => ['required', 'integer', 'exists:attributes,attribute_id'],
            'orders.*.sort_order' => ['required', 'integer'],
        ]);
        foreach ($data['orders'] as $order) {
            Attribute::where('attribute_id', $order['attribute_id'])->update(['sort_order' => $order['sort_order']]);
        }
        return response()->json(['success' => true]);
    }
}
