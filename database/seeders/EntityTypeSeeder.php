<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntityType;
use App\Models\Attribute;
use App\Models\AttributeGroup;

class EntityTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Entity Type
        $customerType = EntityType::create([
            'type_name' => 'Khách hàng',
            'type_code' => 'customer',
            'description' => 'Quản lý thông tin khách hàng',
            'is_active' => true
        ]);

        // Create Attribute Groups
        $basicGroup = AttributeGroup::create([
            'entity_type_id' => $customerType->entity_type_id,
            'group_code' => 'basic_info',
            'group_name' => 'Thông tin cơ bản',
            'sort_order' => 1,
            'is_active' => true
        ]);

        $contactGroup = AttributeGroup::create([
            'entity_type_id' => $customerType->entity_type_id,
            'group_code' => 'contact_info',
            'group_name' => 'Thông tin liên hệ',
            'sort_order' => 2,
            'is_active' => true
        ]);

        // Create Attributes
        $attributes = [
            [
                'entity_type_id' => $customerType->entity_type_id,
                'group_id' => $basicGroup->group_id,
                'attribute_code' => 'customer_name',
                'attribute_label' => 'Tên khách hàng',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 1,
                'placeholder' => 'Nhập tên khách hàng'
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'group_id' => $basicGroup->group_id,
                'attribute_code' => 'customer_code',
                'attribute_label' => 'Mã khách hàng',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_unique' => true,
                'is_searchable' => true,
                'sort_order' => 2,
                'placeholder' => 'Nhập mã khách hàng'
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'group_id' => $basicGroup->group_id,
                'attribute_code' => 'birth_date',
                'attribute_label' => 'Ngày sinh',
                'backend_type' => 'datetime',
                'frontend_input' => 'text',
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 3
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'group_id' => $contactGroup->group_id,
                'attribute_code' => 'email',
                'attribute_label' => 'Email',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => false,
                'is_unique' => true,
                'is_searchable' => true,
                'sort_order' => 1,
                'placeholder' => 'Nhập email'
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'group_id' => $contactGroup->group_id,
                'attribute_code' => 'phone',
                'attribute_label' => 'Số điện thoại',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 2,
                'placeholder' => 'Nhập số điện thoại'
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'group_id' => $contactGroup->group_id,
                'attribute_code' => 'address',
                'attribute_label' => 'Địa chỉ',
                'backend_type' => 'text',
                'frontend_input' => 'textarea',
                'is_required' => false,
                'is_searchable' => true,
                'sort_order' => 3,
                'placeholder' => 'Nhập địa chỉ'
            ]
        ];

        foreach ($attributes as $attributeData) {
            Attribute::create($attributeData);
        }
    }
}