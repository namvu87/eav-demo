<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Services\EavService;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    public function treeByType(int $typeId)
    {
        // Return the flat list ordered by path; client can build nested tree
        $items = Entity::where('entity_type_id', $typeId)
            ->orderBy('path')
            ->get(['entity_id','entity_type_id','entity_code','entity_name','parent_id','path','level','is_active','sort_order']);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function children(int $id)
    {
        $children = Entity::where('parent_id', $id)->orderBy('sort_order')->orderBy('entity_name')->get();
        return response()->json(['success' => true, 'data' => $children]);
    }

    public function descendants(int $id)
    {
        $entity = Entity::findOrFail($id);
        $descendants = $entity->getDescendants();
        return response()->json(['success' => true, 'data' => $descendants]);
    }

    public function ancestors(int $id)
    {
        $entity = Entity::findOrFail($id);
        $ancestors = $entity->getAncestors();
        return response()->json(['success' => true, 'data' => $ancestors]);
    }

    public function move(int $id, Request $request, EavService $eavService)
    {
        $data = $request->validate([
            'new_parent_id' => ['nullable', 'integer', 'exists:entities,entity_id']
        ]);
        $entity = Entity::findOrFail($id);
        $eavService->moveEntity($entity, $data['new_parent_id'] ?? null);
        return response()->json(['success' => true, 'data' => $entity->refresh()]);
    }
}
