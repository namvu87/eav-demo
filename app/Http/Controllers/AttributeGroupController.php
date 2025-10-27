<?php

namespace App\Http\Controllers;

use App\Models\AttributeGroup;
use App\Models\EntityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeGroupController extends Controller
{
    /**
     * Display a listing of attribute groups
     */
    public function index(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        $search = $request->get('search');

        $query = AttributeGroup::with(['entityType', 'attributes'])
            ->when($entityTypeId, function($q) use ($entityTypeId) {
                return $q->where('entity_type_id', $entityTypeId);
            })
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->where('group_name', 'like', "%{$search}%")
                          ->orWhere('group_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('entity_type_id')
            ->orderBy('sort_order')
            ->orderBy('group_name');

        $attributeGroups = $query->paginate(20);
        $entityTypes = EntityType::orderBy('type_name')->get();

        return view('attribute-groups.index', [
            'attributeGroups' => $attributeGroups,
            'entityTypes' => $entityTypes,
            'title' => 'Attribute Groups'
        ]);
    }

    /**
     * Show the form for creating a new attribute group
     */
    public function create(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        
        $entityTypes = EntityType::orderBy('type_name')->get();

        return view('attribute-groups.create', [
            'entityTypes' => $entityTypes,
            'entityTypeId' => $entityTypeId,
            'title' => 'Create Attribute Group'
        ]);
    }

    /**
     * Store a newly created attribute group
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity_type_id' => 'required|exists:entity_types,entity_type_id',
            'group_code' => 'required|string|max:100|unique:attribute_groups,group_code',
            'group_name' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            AttributeGroup::create([
                'entity_type_id' => $request->entity_type_id,
                'group_code' => $request->group_code,
                'group_name' => $request->group_name,
                'sort_order' => $request->get('sort_order', 0),
                'is_active' => $request->boolean('is_active', true)
            ]);

            return redirect()->route('attribute-groups.index')
                ->with('success', 'Attribute group created successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified attribute group
     */
    public function show($id)
    {
        $attributeGroup = AttributeGroup::with(['entityType', 'attributes'])
            ->findOrFail($id);

        return view('attribute-groups.show', [
            'attributeGroup' => $attributeGroup,
            'title' => 'Attribute Group Details'
        ]);
    }

    /**
     * Show the form for editing the specified attribute group
     */
    public function edit($id)
    {
        $attributeGroup = AttributeGroup::findOrFail($id);
        $entityTypes = EntityType::orderBy('type_name')->get();

        return view('attribute-groups.edit', [
            'attributeGroup' => $attributeGroup,
            'entityTypes' => $entityTypes,
            'title' => 'Edit Attribute Group'
        ]);
    }

    /**
     * Update the specified attribute group
     */
    public function update(Request $request, $id)
    {
        $attributeGroup = AttributeGroup::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'entity_type_id' => 'required|exists:entity_types,entity_type_id',
            'group_code' => 'required|string|max:100|unique:attribute_groups,group_code,' . $id . ',group_id',
            'group_name' => 'required|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $attributeGroup->update([
                'entity_type_id' => $request->entity_type_id,
                'group_code' => $request->group_code,
                'group_name' => $request->group_name,
                'sort_order' => $request->get('sort_order', 0),
                'is_active' => $request->boolean('is_active', true)
            ]);

            return redirect()->route('attribute-groups.index')
                ->with('success', 'Attribute group updated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified attribute group
     */
    public function destroy($id)
    {
        $attributeGroup = AttributeGroup::findOrFail($id);
        
        try {
            // Check if group has attributes
            if ($attributeGroup->attributes()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete attribute group that has attributes. Please move or delete the attributes first.']);
            }

            $attributeGroup->delete();
            
            return redirect()->route('attribute-groups.index')
                ->with('success', 'Attribute group deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get attribute groups for specific entity type (API)
     */
    public function getByEntityType($entityTypeId)
    {
        $attributeGroups = AttributeGroup::where('entity_type_id', $entityTypeId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('group_name')
            ->get();

        return response()->json($attributeGroups);
    }
}
