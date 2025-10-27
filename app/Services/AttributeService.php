<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Repositories\AttributeRepository;
use Illuminate\Support\Facades\DB;

class AttributeService
{
    protected $repository;
    
    public function __construct(AttributeRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Create a new attribute with its options
     */
    public function createAttribute(array $data): Attribute
    {
        DB::beginTransaction();
        
        try {
            // Prepare attribute data
            $attributeData = $data;
            
            // Handle validation rules
            if (isset($data['validation_rules'])) {
                $attributeData['validation_rules'] = json_encode($data['validation_rules']);
            }
            
            // Create the attribute
            $attribute = Attribute::create($attributeData);
            
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
            // Prepare attribute data
            $attributeData = $data;
            
            // Handle validation rules
            if (isset($data['validation_rules'])) {
                $attributeData['validation_rules'] = json_encode($data['validation_rules']);
            }
            
            // Update the attribute
            $attribute->update($attributeData);
            
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
        return $this->repository->getByEntityType($entityTypeId);
    }

    /**
     * Get all attributes
     */
    public function getAllAttributes(array $filters = [])
    {
        return $this->repository->getWithFilters($filters);
    }
}
