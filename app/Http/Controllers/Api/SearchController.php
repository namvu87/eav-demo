<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Entity;
use App\Services\EavService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = (string) $request->query('q', '');
        $query = Entity::query()
            ->where(function ($sub) use ($q) {
                $sub->where('entity_code', 'like', "%$q%")
                    ->orWhere('entity_name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            })
            ->orderBy('entity_type_id')
            ->orderBy('path');

        return response()->json(['success' => true, 'data' => $query->limit(100)->get()]);
    }

    public function filter(Request $request, EavService $eavService)
    {
        $data = $request->validate([
            'entity_type_id' => ['required', 'integer', 'exists:entity_types,entity_type_id'],
            'filters' => ['required', 'array']
        ]);

        $results = $eavService->searchEntities($data['entity_type_id'], $data['filters']);
        return response()->json(['success' => true, 'data' => $results]);
    }
}
