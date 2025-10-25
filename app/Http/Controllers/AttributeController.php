<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\EntityType;
use App\Models\AttributeOption;
use App\Services\AttributeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    /**
     * Display a listing of attributes
     */
    public function index(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        $groupId = $request->get('group_id');
        $search = $request->get('search');

        $query = Attribute::with(['entityType', 'group', 'options'])
            ->when($entityTypeId, function($q) use ($entityTypeId) {
                return $q->where('entity_type_id', $entityTypeId);
            })
            ->when($groupId, function($q) use ($groupId) {
                return $q->where('group_id', $groupId);
            })
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->where('attribute_label', 'like', "%{$search}%")
                          ->orWhere('attribute_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order');

        $attributes = $query->paginate(20);

        $entityTypes = EntityType::orderBy('type_name')->get();
        $attributeGroups = AttributeGroup::orderBy('group_name')->get();

        return Inertia::render('Attributes/Index', [
            'attributes' => $attributes,
            'entityTypes' => $entityTypes,
            'attributeGroups' => $attributeGroups,
            'filters' => [
                'entity_type_id' => $entityTypeId,
                'group_id' => $groupId,
                'search' => $search
            ]
        ]);
    }

    /**
     * Show the form for creating a new attribute
     */
    public function create(Request $request)
    {
        $entityTypeId = $request->get('entity_type_id');
        
        $entityTypes = EntityType::orderBy('type_name')->get();
        $attributeGroups = AttributeGroup::orderBy('group_name')->get();

        return Inertia::render('Attributes/Create', [
            'entityTypes' => $entityTypes,
            'attributeGroups' => $attributeGroups,
            'entityTypeId' => $entityTypeId
        ]);
    }

    /**
     * Store a newly created attribute
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity_type_id' => 'nullable|exists:entity_types,entity_type_id',
            'attribute_code' => 'required|string|max:255',
            'attribute_label' => 'required|string|max:255',
            'backend_type' => 'required|in:varchar,text,int,decimal,datetime,file',
            'frontend_input' => 'required|string|max:255',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_searchable' => 'boolean',
            'is_filterable' => 'boolean',
            'default_value' => 'nullable|string',
            'validation_rules' => 'nullable|array',
            'max_file_count' => 'nullable|integer|min:1',
            'allowed_extensions' => 'nullable|string',
            'max_file_size_kb' => 'nullable|integer|min:1',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'frontend_class' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'group_id' => 'nullable|exists:attribute_groups,group_id',
            'is_system' => 'boolean',
            'is_user_defined' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->attributeService->createAttribute($request->all());
            
            return redirect()->route('attributes.index')
                ->with('success', 'Thuộc tính đã được tạo thành công.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified attribute
     */
    public function show($id)
    {
        $attribute = Attribute::with(['entityType', 'group', 'options'])->findOrFail($id);

        return Inertia::render('Attributes/Show', [
            'attribute' => $attribute
        ]);
    }

    /**
     * Show the form for editing the specified attribute
     */
    public function edit($id)
    {
        $attribute = Attribute::with(['entityType', 'group', 'options'])->findOrFail($id);
        $entityTypes = EntityType::orderBy('type_name')->get();
        $attributeGroups = AttributeGroup::orderBy('group_name')->get();

        return Inertia::render('Attributes/Edit', [
            'attribute' => $attribute,
            'entityTypes' => $entityTypes,
            'attributeGroups' => $attributeGroups
        ]);
    }

    /**
     * Update the specified attribute
     */
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'entity_type_id' => 'nullable|exists:entity_types,entity_type_id',
            'attribute_code' => 'required|string|max:255',
            'attribute_label' => 'required|string|max:255',
            'backend_type' => 'required|in:varchar,text,int,decimal,datetime,file',
            'frontend_input' => 'required|string|max:255',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_searchable' => 'boolean',
            'is_filterable' => 'boolean',
            'default_value' => 'nullable|string',
            'validation_rules' => 'nullable|array',
            'max_file_count' => 'nullable|integer|min:1',
            'allowed_extensions' => 'nullable|string',
            'max_file_size_kb' => 'nullable|integer|min:1',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'frontend_class' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'group_id' => 'nullable|exists:attribute_groups,group_id',
            'is_system' => 'boolean',
            'is_user_defined' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->attributeService->updateAttribute($attribute, $request->all());
            
            return redirect()->route('attributes.index')
                ->with('success', 'Thuộc tính đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified attribute
     */
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        
        try {
            $this->attributeService->deleteAttribute($attribute);
            
            return redirect()->route('attributes.index')
                ->with('success', 'Thuộc tính đã được xóa thành công.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
