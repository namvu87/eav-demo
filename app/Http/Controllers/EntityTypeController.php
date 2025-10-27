<?php

namespace App\Http\Controllers;

use App\Models\EntityType;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Repositories\AttributeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntityTypeController extends Controller
{
    protected $attributeRepository;
    
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Display a listing of entity types
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = EntityType::with(['attributes' => function($query) {
            $query->orderBy('sort_order');
        }])
        ->when($search, function($q) use ($search) {
            return $q->where(function($query) use ($search) {
                $query->where('type_name', 'like', "%{$search}%")
                      ->orWhere('type_code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        })
        ->orderBy('type_name');

        $entityTypes = $query->get();

        return view('entity-types.index', [
            'entityTypes' => $entityTypes,
            'title' => 'Entity Types - EAV'
        ]);
    }

    /**
     * Show the form for creating a new entity type
     */
    public function create()
    {
        $attributes = Attribute::orderBy('attribute_label')->get();
        
        return view('entity-types.create', [
            'attributes' => $attributes,
            'title' => 'Create Entity Type - EAV'
        ]);
    }

    /**
     * Store a newly created entity type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255',
            'type_code' => 'required|string|max:255|unique:entity_types,type_code',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $entityType = EntityType::create($request->only([
            'type_name', 'type_code', 'description', 'is_active'
        ]));

        return redirect()->route('entity-types.show', $entityType->entity_type_id)
            ->with('success', 'Entity type created successfully.');
    }

    /**
     * Display the specified entity type
     */
    public function show($id)
    {
        $entityType = EntityType::with(['attributes' => function($query) {
            $query->with('group')->orderBy('sort_order');
        }])->findOrFail($id);

        return view('entity-types.show', [
            'entityType' => $entityType,
            'title' => 'View Entity Type'
        ]);
    }

    /**
     * Show the form for editing the specified entity type
     */
    public function edit($id)
    {
        $entityType = EntityType::findOrFail($id);
        $attributeGroups = AttributeGroup::orderBy('group_name')->get();

        return view('entity-types.edit', [
            'entityType' => $entityType,
            'attributeGroups' => $attributeGroups,
            'title' => 'Edit Entity Type'
        ]);
    }

    /**
     * Update the specified entity type
     */
    public function update(Request $request, $id)
    {
        $entityType = EntityType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255',
            'type_code' => 'required|string|max:255|unique:entity_types,type_code,' . $id . ',entity_type_id',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $entityType->update($request->only([
            'type_name', 'type_code', 'description', 'is_active'
        ]));

        return redirect()->route('entity-types.show', $entityType->entity_type_id)
            ->with('success', 'Entity type updated successfully.');
    }

    /**
     * Remove the specified entity type
     */
    public function destroy($id)
    {
        $entityType = EntityType::findOrFail($id);
        
        // Check if entity type has entities
        if ($entityType->entities()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete entity type with existing entities.']);
        }

        $entityType->delete();

        return redirect()->route('entity-types.index')
            ->with('success', 'Entity type deleted successfully.');
    }

    /**
     * Get management interface for specific entity type
     */
    public function manage($id)
    {
        $entityType = EntityType::with(['attributes' => function($query) {
            $query->with('group')->orderBy('sort_order');
        }])->findOrFail($id);

        // Get entities of this type with pagination
        $entities = $entityType->entities()
            ->with(['parent', 'entityType'])
            ->orderBy('sort_order')
            ->orderBy('entity_name')
            ->paginate(20);

        return view('entity-types.manage', [
            'entityType' => $entityType,
            'entities' => $entities,
            'title' => 'Manage Entity Type'
        ]);
    }

    /**
     * Get attributes for a specific entity type
     */
    public function getAttributes($id)
    {
        $entityType = EntityType::findOrFail($id);
        
        // Use repository to get attributes
        $attributes = $this->attributeRepository->getByEntityType($id);

        return response()->json([
            'entity_type' => $entityType,
            'attributes' => $attributes
        ]);
    }
}
