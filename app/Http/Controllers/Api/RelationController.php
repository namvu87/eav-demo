<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntityRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'source_entity_id' => ['required', 'integer', 'exists:entities,entity_id'],
            'target_entity_id' => ['required', 'integer', 'exists:entities,entity_id', 'different:source_entity_id'],
            'relation_type' => ['required', 'string', 'max:100'],
            'relation_data' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Prevent duplicate relation (source, target, type)
        $dup = EntityRelation::where('source_entity_id', $data['source_entity_id'])
            ->where('target_entity_id', $data['target_entity_id'])
            ->where('relation_type', $data['relation_type'])
            ->exists();
        if ($dup) {
            return response()->json(['success' => false, 'message' => 'Relation already exists'], 422);
        }

        $relation = EntityRelation::create($data);
        return response()->json(['success' => true, 'data' => $relation], 201);
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'source_entity_id' => ['required', 'integer', 'exists:entities,entity_id'],
            'relation_type' => ['required', 'string', 'max:100'],
            'targets' => ['required', 'array'],
            'targets.*.target_entity_id' => ['required', 'integer', 'exists:entities,entity_id'],
            'targets.*.relation_data' => ['nullable', 'array'],
        ]);

        $created = [];
        DB::beginTransaction();
        try {
            foreach ($data['targets'] as $t) {
                $exists = EntityRelation::where('source_entity_id', $data['source_entity_id'])
                    ->where('target_entity_id', $t['target_entity_id'])
                    ->where('relation_type', $data['relation_type'])
                    ->exists();
                if ($exists) {
                    continue;
                }
                $created[] = EntityRelation::create([
                    'source_entity_id' => $data['source_entity_id'],
                    'target_entity_id' => $t['target_entity_id'],
                    'relation_type' => $data['relation_type'],
                    'relation_data' => $t['relation_data'] ?? null,
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['success' => true, 'data' => $created], 201);
    }

    public function listByEntity(int $id)
    {
        $outgoing = EntityRelation::where('source_entity_id', $id)->with('targetEntity.entityType')->get();
        $incoming = EntityRelation::where('target_entity_id', $id)->with('sourceEntity.entityType')->get();
        return response()->json(['success' => true, 'data' => [
            'outgoing_relations' => $outgoing,
            'incoming_relations' => $incoming,
        ]]);
    }

    public function listByEntityAndType(int $id, string $type)
    {
        $rels = EntityRelation::where('source_entity_id', $id)->where('relation_type', $type)->with('targetEntity.entityType')->get();
        return response()->json(['success' => true, 'data' => $rels]);
    }

    public function update(Request $request, int $id)
    {
        $relation = EntityRelation::findOrFail($id);
        $data = $request->validate([
            'relation_data' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $relation->update($data);
        return response()->json(['success' => true, 'data' => $relation]);
    }

    public function destroy(int $id)
    {
        $relation = EntityRelation::findOrFail($id);
        $relation->delete();
        return response()->json(['success' => true]);
    }
}
