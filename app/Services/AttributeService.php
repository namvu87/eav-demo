<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB;

class AttributeService
{
    /**
     * Create a new attribute with its options
     */
    public function createAttribute(array $data): Attribute
    {
        DB::beginTransaction();
        
        try {
            // Create the attribute
            $attribute = Attribute::create($data);
            
            // Handle options for select/multiselect
            if (in_array($data['frontend_input'] ?? '', ['select', 'multiselect']) && isset($data['options'])) {
                $this->saveOptions($attribute, $data['options']);
            }
            
            DB::commit();
            return $attribute;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing attribute
     */
    public function updateAttribute(Attribute $attribute, array $data): Attribute
    {
        DB::beginTransaction();
        
        try {
            // Update the attribute
            $attribute->update($data);
            
            // Handle options for select/multiselect
            if (in_array($data['frontend_input'] ?? '', ['select', 'multiselect']) && isset($data['options'])) {
                // Delete existing options
                $attribute->options()->delete();
                
                // Create new options
                $this->saveOptions($attribute, $data['options']);
            } elseif (in_array($data['frontend_input'] ?? '', ['select', 'multiselect'])) {
                // No options provided, delete existing ones
                $attribute->options()->delete();
            }
            
            DB::commit();
            return $attribute;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an attribute
     */
    public function deleteAttribute(Attribute $attribute): bool
    {
        DB::beginTransaction();
        
        try {
            // Check if attribute has values
            if ($attribute->hasValues()) {
                throw new \Exception('Không thể xóa thuộc tính có giá trị hiện tại.');
            }
            
            // Delete options first
            $attribute->options()->delete();
            
            // Delete the attribute
            $attribute->delete();
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Save options for an attribute
     */
    private function saveOptions(Attribute $attribute, array $options): void
    {
        foreach ($options as $optionData) {
            if (!empty($optionData['option_value'])) {
                AttributeOption::create([
                    'attribute_id' => $attribute->attribute_id,
                    'option_value' => $optionData['option_value'],
                    'option_label' => $optionData['option_label'] ?? $optionData['option_value'],
                    'sort_order' => $optionData['sort_order'] ?? 0,
                    'is_default' => $optionData['is_default'] ?? false
                ]);
            }
        }
    }

    /**
     * Get attributes by entity type
     */
    public function getAttributesByEntityType($entityTypeId)
    {
        return Attribute::where('entity_type_id', $entityTypeId)
            ->with('options')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all attributes
     */
    public function getAllAttributes(array $filters = [])
    {
        $query = Attribute::with(['entityType', 'group', 'options']);
        
        if (isset($filters['entity_type_id'])) {
            $query->where('entity_type_id', $filters['entity_type_id']);
        }
        
        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }
        
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('attribute_code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('attribute_label', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('sort_order')->paginate(15);
    }
}
