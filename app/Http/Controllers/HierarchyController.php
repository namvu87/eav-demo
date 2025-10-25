<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\EntityType;
use App\Services\EavService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class HierarchyController extends Controller
{
    protected $eavService;

    public function __construct(EavService $eavService)
    {
        $this->eavService = $eavService;
    }

    /**
     * Display the hierarchy tree
     */
    public function index(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        
        // Get root entities (entities without parent)
        $query = Entity::with(['entityType', 'children' => function($query) {
            $query->with(['entityType', 'children'])->orderBy('sort_order')->orderBy('entity_name');
        }])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('entity_name');

        if ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId);
        }

        $hierarchies = $query->get();

        // Get entity types for filter
        $entityTypes = EntityType::orderBy('type_name')->get();

        return Inertia::render('EntityTypes/Hierarchy', [
            'hierarchies' => $hierarchies,
            'entityTypes' => $entityTypes
        ]);
    }

    /**
     * Show the form for creating a new child entity
     */
    public function create(Request $request)
    {
        $parentId = $request->get('parent_id');
        $entityTypeId = $request->get('entity_type_id');
        
        $entityTypes = EntityType::orderBy('type_name')->get();
        $parent = $parentId ? Entity::with('entityType')->find($parentId) : null;

        return Inertia::render('EntityTypes/CreateChild', [
            'entityTypes' => $entityTypes,
            'parent' => $parent,
            'entityTypeId' => $entityTypeId
        ]);
    }

    /**
     * Store a newly created child entity
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity_type_id' => 'required|exists:entity_types,entity_type_id',
            'parent_id' => 'nullable|exists:entities,entity_id',
            'entity_code' => 'required|string|max:255',
            'entity_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'attributes' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $entity = new Entity($request->only([
                'entity_type_id', 'parent_id', 'entity_code', 'entity_name', 'is_active'
            ]));

            $entity->is_active = $request->boolean('is_active', true);
            $entity->sort_order = 0;

            // Validate attributes
            $attributeErrors = $this->eavService->validateEntityAttributes(
                $entity->entity_type_id, 
                $request->get('attributes', [])
            );

            if (!empty($attributeErrors)) {
                return back()->withErrors($attributeErrors)->withInput();
            }

            $this->eavService->saveEntityWithAttributes($entity, $request->get('attributes', []));

            return redirect()->route('hierarchy.index')
                ->with('success', 'Child entity created successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified entity and all its children
     */
    public function destroy($id)
    {
        $entity = Entity::findOrFail($id);
        
        // Check if entity has children
        if ($entity->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete entity that has children. Please delete children first.');
        }
        
        $entity->delete();
        
        return redirect()->route('hierarchy.index')
            ->with('success', 'Entity deleted successfully.');
    }

    /**
     * Move entity to different parent
     */
    public function move(Request $request, $id)
    {
        try {
            $entity = Entity::findOrFail($id);
            $newParentId = $request->get('parent_id');
            
            $this->eavService->moveEntity($entity, $newParentId);
            
            return redirect()->route('hierarchy.index')
                ->with('success', 'Entity moved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }


    /**
     * Get entity tree for API
     */
    public function getTree(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        
        $query = Entity::with(['entityType', 'children' => function($query) {
            $query->with(['entityType', 'children'])->orderBy('sort_order')->orderBy('entity_name');
        }])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('entity_name');

        if ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId);
        }

        $hierarchies = $query->get();

        return response()->json([
            'hierarchies' => $hierarchies
        ]);
    }
}
