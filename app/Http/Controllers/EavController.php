<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\EntityType;
use App\Models\Attribute;
use App\Services\EavService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EavController extends Controller
{
    protected $eavService;

    public function __construct(EavService $eavService)
    {
        $this->eavService = $eavService;
    }

    /**
     * Display a listing of entities
     */
    public function index(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        $search = $request->get('search');
        $parentId = $request->get('parent_id');

        $query = Entity::with(['entityType', 'parent'])
            ->when($entityTypeId, function($q) use ($entityTypeId) {
                return $q->where('entity_type_id', $entityTypeId);
            })
            ->when($parentId !== null, function($q) use ($parentId) {
                return $parentId ? $q->where('parent_id', $parentId) : $q->whereNull('parent_id');
            })
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->where('entity_name', 'like', "%{$search}%")
                          ->orWhere('entity_code', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('entity_name');

        $entities = $query->paginate(20);

        // Get entity types for filter
        $entityTypes = EntityType::orderBy('type_name')->get();

        return view('eav.index', [
            'entities' => $entities,
            'entityTypes' => $entityTypes,
            'title' => 'EAV Entities'
        ]);
    }

    /**
     * Show the form for creating a new entity
     */
    public function create(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        $parentId = $request->get('parent_id');

        $entityTypes = EntityType::orderBy('type_name')->get();
        $attributes = collect();
        $parent = null;

        if ($entityTypeId) {
            $attributes = Attribute::where(function($query) use ($entityTypeId) {
                $query->where('entity_type_id', $entityTypeId)
                      ->orWhereNull('entity_type_id');
            })
            ->with('options')
            ->orderBy('sort_order')
            ->get();
        }

        if ($parentId) {
            $parent = Entity::with('entityType')->find($parentId);
        }

        return view('eav.create', [
            'entityTypes' => $entityTypes,
            'attributes' => $attributes,
            'parent' => $parent,
            'entityTypeId' => $entityTypeId,
            'parentId' => $parentId,
            'title' => 'Create Entity - EAV'
        ]);
    }

    /**
     * Store a newly created entity
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity_type_id' => 'required|exists:entity_types,entity_type_id',
            'entity_code' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:entities,entity_id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'attributes' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $entity = new Entity($request->only([
                'entity_type_id', 'entity_code', 'entity_name', 
                'parent_id', 'description', 'is_active', 'sort_order'
            ]));

            $entity->is_active = $request->boolean('is_active', true);
            $entity->sort_order = $request->get('sort_order', 0);

            // Validate attributes
            $attributeErrors = $this->eavService->validateEntityAttributes(
                $entity->entity_type_id, 
                $request->get('attributes', [])
            );

            if (!empty($attributeErrors)) {
                return back()->withErrors($attributeErrors)->withInput();
            }

            $this->eavService->saveEntityWithAttributes($entity, $request->get('attributes', []));

            return redirect()->route('eav.show', $entity->entity_id)
                ->with('success', 'Thực thể đã được tạo thành công.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified entity
     */
    public function show($id)
    {
        $entityData = $this->eavService->getEntityWithAttributes($id);
        
        return Inertia::render('EAV/Show', [
            'entity' => $entityData['entity'],
            'attributes' => $entityData['attributes']
        ]);
    }

    /**
     * Show the form for editing the specified entity
     */
    public function edit($id)
    {
        $entity = Entity::with('entityType')->findOrFail($id);
        
        $attributes = Attribute::where(function($query) use ($entity) {
            $query->where('entity_type_id', $entity->entity_type_id)
                  ->orWhereNull('entity_type_id');
        })
        ->with('options')
        ->orderBy('sort_order')
        ->get();

        // Get current attribute values
        $attributeValues = [];
        foreach ($attributes as $attribute) {
            $value = $entity->getEavAttributeValue($attribute->attribute_code);
            $attributeValues['attr_' . $attribute->attribute_id] = $value;
        }

        // Get entity types for dropdown
        $entityTypes = EntityType::orderBy('type_name')->get();
        
        // Get current attribute values in the format expected by EntityForm
        $currentAttributes = [];
        foreach ($attributes as $attribute) {
            $value = $entity->getEavAttributeValue($attribute->attribute_code);
            $currentAttributes[$attribute->attribute_code] = $value;
        }
        
        $entity->attributes = $currentAttributes;

        return Inertia::render('EntityTypes/EntityForm', [
            'entity' => $entity,
            'entityTypes' => $entityTypes,
            'entityTypeId' => $entity->entity_type_id
        ]);
    }

    /**
     * Update the specified entity
     */
    public function update(Request $request, $id)
    {
        $entity = Entity::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'entity_code' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:entities,entity_id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'attributes' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $entity->fill($request->only([
                'entity_code', 'entity_name', 'parent_id', 
                'description', 'is_active', 'sort_order'
            ]));

            // Validate attributes
            $attributeErrors = $this->eavService->validateEntityAttributes(
                $entity->entity_type_id, 
                $request->get('attributes', [])
            );

            if (!empty($attributeErrors)) {
                return back()->withErrors($attributeErrors)->withInput();
            }

            $this->eavService->saveEntityWithAttributes($entity, $request->get('attributes', []));

            return redirect()->route('eav.show', $entity->entity_id)
                ->with('success', 'Thực thể đã được cập nhật thành công.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified entity
     */
    public function destroy($id)
    {
        try {
            $entity = Entity::findOrFail($id);
            
            // Check if entity has children
            if ($entity->children()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete entity with children. Please delete children first.']);
            }

            $entity->delete();

            return redirect()->route('eav.index')
                ->with('success', 'Thực thể đã được xóa thành công.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Search entities by attribute values
     */
    public function search(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        $filters = $request->except(['entity_type_id']);

        if (!$entityTypeId) {
            return response()->json(['entities' => []]);
        }

        $entities = $this->eavService->searchEntities($entityTypeId, $filters);

        return response()->json(['entities' => $entities]);
    }

    /**
     * Get entity tree for parent selection
     */
    public function tree(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        $excludeId = $request->get('exclude_id');

        $query = Entity::where('is_active', true)
            ->when($entityTypeId, function($q) use ($entityTypeId) {
                return $q->where('entity_type_id', $entityTypeId);
            })
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('entity_id', '!=', $excludeId);
            })
            ->orderBy('path');

        $entities = $query->get();

        return response()->json(['entities' => $entities]);
    }

    /**
     * Count entities by entity type
     */
    public function count(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        
        $count = Entity::where('entity_type_id', $entityTypeId)->count();
        
        return response()->json(['count' => $count]);
    }
}
