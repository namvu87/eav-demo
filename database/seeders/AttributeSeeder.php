<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EntityType;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\AttributeOptionValue;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createProductAttributes();
        $this->createCustomerAttributes();
    }

    /**
     * Tạo attributes cho Sản phẩm
     */
    private function createProductAttributes(): void
    {
        $productType = EntityType::where('type_code', 'product')->first();

        if (!$productType) {
            $this->command->error('Entity Type "product" not found!');
            return;
        }

        // Sản phẩm 1: Text và Số tiền
        $attributes = [
            [
                'entity_type_id' => $productType->entity_type_id,
                'attribute_code' => 'hang_1_text',
                'attribute_label' => 'Hàng 1 - Thông tin',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_unique' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'placeholder' => 'Nhập thông tin hàng 1',
                'help_text' => 'Thông tin chi tiết về sản phẩm',
                'sort_order' => 1,
            ],
            [
                'entity_type_id' => $productType->entity_type_id,
                'attribute_code' => 'hang_2_so_tien',
                'attribute_label' => 'Hàng 2 - Số tiền',
                'backend_type' => 'decimal',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_unique' => false,
                'is_searchable' => false,
                'is_filterable' => true,
                'placeholder' => 'Nhập giá sản phẩm',
                'help_text' => 'Giá bán sản phẩm (VND)',
                'validation_rules' => [
                    'min' => 0,
                    'max' => 999999999,
                ],
                'sort_order' => 2,
            ],
            // Sản phẩm 2: Ảnh và File
            [
                'entity_type_id' => $productType->entity_type_id,
                'attribute_code' => 'hang_3_anh',
                'attribute_label' => 'Hàng 3 - Hình ảnh',
                'backend_type' => 'file',
                'frontend_input' => 'file',
                'is_required' => false,
                'is_unique' => false,
                'is_searchable' => false,
                'is_filterable' => false,
                'max_file_count' => 5,
                'allowed_extensions' => 'jpg,jpeg,png,gif,webp',
                'max_file_size_kb' => 5120,
                'help_text' => 'Upload ảnh sản phẩm (tối đa 5 ảnh, 5MB/ảnh)',
                'sort_order' => 3,
            ],
            [
                'entity_type_id' => $productType->entity_type_id,
                'attribute_code' => 'hang_4_file',
                'attribute_label' => 'Hàng 4 - Tài liệu',
                'backend_type' => 'file',
                'frontend_input' => 'file',
                'is_required' => false,
                'is_unique' => false,
                'is_searchable' => false,
                'is_filterable' => false,
                'max_file_count' => 3,
                'allowed_extensions' => 'pdf,doc,docx,xls,xlsx',
                'max_file_size_kb' => 10240,
                'help_text' => 'Upload tài liệu kỹ thuật (tối đa 3 file, 10MB/file)',
                'sort_order' => 4,
            ],
        ];

        foreach ($attributes as $attrData) {
            Attribute::updateOrCreate(
                [
                    'entity_type_id' => $attrData['entity_type_id'],
                    'attribute_code' => $attrData['attribute_code'],
                ],
                $attrData
            );
        }

        $this->command->info('✅ Created 4 attributes for Sản phẩm');
    }

    /**
     * Tạo attributes cho Khách hàng
     */
    private function createCustomerAttributes(): void
    {
        $customerType = EntityType::where('type_code', 'customer')->first();

        if (!$customerType) {
            $this->command->error('Entity Type "customer" not found!');
            return;
        }

        // Khách hàng 1: Họ tên và Số điện thoại
        $attributes = [
            [
                'entity_type_id' => $customerType->entity_type_id,
                'attribute_code' => 'hang_1_ho_ten',
                'attribute_label' => 'Hàng 1 - Họ và tên',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_unique' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'placeholder' => 'Nhập họ và tên khách hàng',
                'help_text' => 'Họ tên đầy đủ của khách hàng',
                'validation_rules' => [
                    'min' => 3,
                    'max' => 100,
                ],
                'sort_order' => 1,
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'attribute_code' => 'hang_2_so_dien_thoai',
                'attribute_label' => 'Hàng 2 - Số điện thoại',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_unique' => true,
                'is_searchable' => true,
                'is_filterable' => true,
                'placeholder' => 'Nhập số điện thoại',
                'help_text' => 'Số điện thoại liên hệ (10 số)',
                'validation_rules' => [
                    'regex' => '/^[0-9]{10}$/',
                ],
                'sort_order' => 2,
            ],
        ];

        foreach ($attributes as $attrData) {
            Attribute::updateOrCreate(
                [
                    'entity_type_id' => $attrData['entity_type_id'],
                    'attribute_code' => $attrData['attribute_code'],
                ],
                $attrData
            );
        }

        // Khách hàng 2: Quản lý (text) và Giới tính (select)
        $attributes2 = [
            [
                'entity_type_id' => $customerType->entity_type_id,
                'attribute_code' => 'hang_1_quan_ly',
                'attribute_label' => 'Hàng 1 - Người quản lý',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => false,
                'is_unique' => false,
                'is_searchable' => true,
                'is_filterable' => true,
                'placeholder' => 'Nhập tên người quản lý',
                'help_text' => 'Tên nhân viên phụ trách khách hàng này',
                'sort_order' => 3,
            ],
            [
                'entity_type_id' => $customerType->entity_type_id,
                'attribute_code' => 'hang_2_gioi_tinh',
                'attribute_label' => 'Hàng 2 - Giới tính',
                'backend_type' => 'varchar',
                'frontend_input' => 'select',
                'is_required' => true,
                'is_unique' => false,
                'is_searchable' => false,
                'is_filterable' => true,
                'placeholder' => 'Chọn giới tính',
                'help_text' => 'Giới tính của khách hàng',
                'sort_order' => 4,
            ],
        ];

        foreach ($attributes2 as $attrData) {
            $attribute = Attribute::updateOrCreate(
                [
                    'entity_type_id' => $attrData['entity_type_id'],
                    'attribute_code' => $attrData['attribute_code'],
                ],
                $attrData
            );

            // Tạo options cho giới tính
            if ($attrData['attribute_code'] === 'hang_2_gioi_tinh') {
                $this->createGenderOptions($attribute);
            }
        }

        $this->command->info('✅ Created 4 attributes for Khách hàng');
    }

    /**
     * Tạo options cho giới tính
     */
    private function createGenderOptions(Attribute $attribute): void
    {
        $genders = [
            ['value' => 'Nam', 'is_default' => true, 'sort_order' => 1],
            ['value' => 'Nữ', 'is_default' => false, 'sort_order' => 2],
            ['value' => 'Khác', 'is_default' => false, 'sort_order' => 3],
        ];

        // Xóa options cũ nếu có
        $attribute->options()->delete();

        foreach ($genders as $genderData) {
            $option = AttributeOption::create([
                'attribute_id' => $attribute->attribute_id,
                'sort_order' => $genderData['sort_order'],
                'is_default' => $genderData['is_default'],
            ]);

            AttributeOptionValue::create([
                'option_id' => $option->option_id,
                'value' => $genderData['value'],
            ]);
        }

        $this->command->info('  ✓ Created 3 options for Giới tính');
    }
}
