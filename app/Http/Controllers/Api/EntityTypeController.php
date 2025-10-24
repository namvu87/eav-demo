<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntityType;
use Illuminate\Http\Request;

class EntityTypeController extends Controller
{
    public function index()
    {
        return response()->json(EntityType::orderBy('sort_order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type_code' => ['required', 'regex:/^[a-z0-9_]+$/', 'unique:entity_types,type_code'],
            'type_name' => ['required', 'string', 'max:255'],
            'type_name_en' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'code_prefix' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'config' => ['nullable', 'array'],
            'is_system' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ]);

        $entityType = EntityType::create($data);
        return response()->json(['success' => true, 'data' => $entityType], 201);
    }

    public function show(int $id)
    {
        $type = EntityType::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, int $id)
    {
        $type = EntityType::findOrFail($id);
        $data = $request->validate([
            'type_code' => ['sometimes', 'regex:/^[a-z0-9_]+$/', 'unique:entity_types,type_code,' . $type->entity_type_id . ',entity_type_id'],
            'type_name' => ['sometimes', 'string', 'max:255'],
            'type_name_en' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'code_prefix' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'config' => ['nullable', 'array'],
            'is_system' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ]);

        // Prevent editing system types if desired: if ($type->is_system) abort(403);
        $type->update($data);
        return response()->json(['success' => true, 'data' => $type]);
    }

    public function destroy(int $id)
    {
        $type = EntityType::findOrFail($id);
        if ($type->is_system) {
            return response()->json(['success' => false, 'message' => 'Cannot delete system type'], 422);
        }
        // Optionally: ensure no entities exist
        if ($type->entities()->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete type with entities'], 422);
        }
        $type->delete();
        return response()->json(['success' => true]);
    }

    public function stats(int $id)
    {
        $type = EntityType::findOrFail($id);
        $count = $type->entities()->count();
        return response()->json(['success' => true, 'data' => ['entities' => $count]]);
    }
}
