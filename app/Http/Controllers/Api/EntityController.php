<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Services\EavService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntityController extends Controller
{
    public function store(Request $request, EavService $eavService)
    {
        $data = $request->validate([
            'entity_type_id' => ['required', 'integer', 'exists:entity_types,entity_type_id'],
            'entity_code' => ['required', 'string', 'max:100', 'unique:entities,entity_code'],
            'entity_name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:entities,entity_id'],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ]);

        $entity = new Entity($data);

        // Extract attr_* dynamic fields
        $attributeData = collect($request->all())
            ->filter(fn ($v, $k) => str_starts_with($k, 'attr_'))
            ->toArray();

        $entity = $eavService->saveEntityWithAttributes($entity, $attributeData);
        return response()->json(['success' => true, 'data' => $entity], 201);
    }

    public function listByType(int $typeId)
    {
        $items = Entity::where('entity_type_id', $typeId)->orderBy('path')->get();
        return response()->json($items);
    }

    public function show(int $id, EavService $eavService)
    {
        return response()->json($eavService->getEntityWithAttributes($id));
    }

    public function update(Request $request, int $id, EavService $eavService)
    {
        $entity = Entity::findOrFail($id);
        $data = $request->validate([
            'entity_name' => ['sometimes', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:entities,entity_id'],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ]);

        $entity->fill($data);

        // Extract attr_* dynamic fields
        $attributeData = collect($request->all())
            ->filter(fn ($v, $k) => str_starts_with($k, 'attr_'))
            ->toArray();

        $eavService->saveEntityWithAttributes($entity, $attributeData);
        return response()->json(['success' => true, 'data' => $entity->refresh()]);
    }

    public function destroy(int $id)
    {
        $entity = Entity::findOrFail($id);
        $entity->delete();
        return response()->json(['success' => true]);
    }

    public function bulkCreate(Request $request, EavService $eavService)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.entity_type_id' => ['required', 'integer', 'exists:entity_types,entity_type_id'],
            'items.*.entity_code' => ['required', 'string', 'max:100', 'distinct'],
            'items.*.entity_name' => ['required', 'string', 'max:255'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:entities,entity_id'],
        ]);

        $created = [];
        DB::beginTransaction();
        try {
            foreach ($data['items'] as $payload) {
                $attrs = collect($payload)
                    ->filter(fn ($v, $k) => str_starts_with($k, 'attr_'))
                    ->toArray();
                $entity = new Entity(collect($payload)->except(array_keys($attrs))->toArray());
                $created[] = $eavService->saveEntityWithAttributes($entity, $attrs);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['success' => true, 'data' => $created], 201);
    }
}
