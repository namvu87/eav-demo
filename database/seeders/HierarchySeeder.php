<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntityType;
use App\Models\Attribute;
use App\Models\Entity;
use App\Services\EavService;

class HierarchySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Entity Types for Warehouse Hierarchy
        $warehouseType = EntityType::create([
            'type_name' => 'Kho hàng',
            'type_code' => 'warehouse',
            'description' => 'Quản lý kho hàng',
            'is_active' => true
        ]);

        $zoneType = EntityType::create([
            'type_name' => 'Khu vực',
            'type_code' => 'zone',
            'description' => 'Khu vực trong kho',
            'is_active' => true
        ]);

        $areaType = EntityType::create([
            'type_name' => 'Vùng',
            'type_code' => 'area',
            'description' => 'Vùng trong khu',
            'is_active' => true
        ]);

        $shelfType = EntityType::create([
            'type_name' => 'Dãy kệ',
            'type_code' => 'shelf',
            'description' => 'Dãy kệ trong vùng',
            'is_active' => true
        ]);

        // Create Attributes for each type
        $this->createWarehouseAttributes($warehouseType);
        $this->createZoneAttributes($zoneType);
        $this->createAreaAttributes($areaType);
        $this->createShelfAttributes($shelfType);

        // Create sample hierarchy
        $this->createSampleHierarchy($warehouseType, $zoneType, $areaType, $shelfType);
    }

    private function createWarehouseAttributes($entityType)
    {
        $attributes = [
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'warehouse_name',
                'attribute_label' => 'Tên kho',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 1
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'warehouse_code',
                'attribute_label' => 'Mã kho',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_unique' => true,
                'is_searchable' => true,
                'sort_order' => 2
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'capacity',
                'attribute_label' => 'Sức chứa',
                'backend_type' => 'int',
                'frontend_input' => 'text',
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($attributes as $attr) {
            Attribute::create($attr);
        }
    }

    private function createZoneAttributes($entityType)
    {
        $attributes = [
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'zone_name',
                'attribute_label' => 'Tên khu',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 1
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'zone_code',
                'attribute_label' => 'Mã khu',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 2
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'temperature',
                'attribute_label' => 'Nhiệt độ',
                'backend_type' => 'decimal',
                'frontend_input' => 'text',
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($attributes as $attr) {
            Attribute::create($attr);
        }
    }

    private function createAreaAttributes($entityType)
    {
        $attributes = [
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'area_name',
                'attribute_label' => 'Tên vùng',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 1
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'area_code',
                'attribute_label' => 'Mã vùng',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 2
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'area_size',
                'attribute_label' => 'Diện tích (m²)',
                'backend_type' => 'decimal',
                'frontend_input' => 'text',
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($attributes as $attr) {
            Attribute::create($attr);
        }
    }

    private function createShelfAttributes($entityType)
    {
        $attributes = [
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'shelf_name',
                'attribute_label' => 'Tên dãy kệ',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 1
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'shelf_code',
                'attribute_label' => 'Mã dãy kệ',
                'backend_type' => 'varchar',
                'frontend_input' => 'text',
                'is_required' => true,
                'is_searchable' => true,
                'sort_order' => 2
            ],
            [
                'entity_type_id' => $entityType->entity_type_id,
                'attribute_code' => 'shelf_levels',
                'attribute_label' => 'Số tầng',
                'backend_type' => 'int',
                'frontend_input' => 'text',
                'is_required' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($attributes as $attr) {
            Attribute::create($attr);
        }
    }

    private function createSampleHierarchy($warehouseType, $zoneType, $areaType, $shelfType)
    {
        $eavService = new EavService();

        // Create warehouse
        $warehouse = Entity::create([
            'entity_type_id' => $warehouseType->entity_type_id,
            'entity_code' => 'WH-001',
            'entity_name' => 'Kho hàng chính',
            'is_active' => true
        ]);

        $eavService->saveEntityWithAttributes($warehouse, [
            'warehouse_name' => 'Kho hàng chính',
            'warehouse_code' => 'WH-001',
            'capacity' => 10000
        ]);

        // Create zones under warehouse
        $zone1 = Entity::create([
            'entity_type_id' => $zoneType->entity_type_id,
            'parent_id' => $warehouse->entity_id,
            'entity_code' => 'ZONE-A',
            'entity_name' => 'Khu A - Hàng điện tử',
            'is_active' => true
        ]);

        $eavService->saveEntityWithAttributes($zone1, [
            'zone_name' => 'Khu A - Hàng điện tử',
            'zone_code' => 'ZONE-A',
            'temperature' => 18.5
        ]);

        $zone2 = Entity::create([
            'entity_type_id' => $zoneType->entity_type_id,
            'parent_id' => $warehouse->entity_id,
            'entity_code' => 'ZONE-B',
            'entity_name' => 'Khu B - Hàng may mặc',
            'is_active' => true
        ]);

        $eavService->saveEntityWithAttributes($zone2, [
            'zone_name' => 'Khu B - Hàng may mặc',
            'zone_code' => 'ZONE-B',
            'temperature' => 22.0
        ]);

        // Create areas under zone1
        $area1 = Entity::create([
            'entity_type_id' => $areaType->entity_type_id,
            'parent_id' => $zone1->entity_id,
            'entity_code' => 'AREA-A1',
            'entity_name' => 'Vùng A1 - Điện thoại',
            'is_active' => true
        ]);

        $eavService->saveEntityWithAttributes($area1, [
            'area_name' => 'Vùng A1 - Điện thoại',
            'area_code' => 'AREA-A1',
            'area_size' => 150.5
        ]);

        // Create shelves under area1
        $shelf1 = Entity::create([
            'entity_type_id' => $shelfType->entity_type_id,
            'parent_id' => $area1->entity_id,
            'entity_code' => 'SHELF-A1-01',
            'entity_name' => 'Dãy kệ A1-01',
            'is_active' => true
        ]);

        $eavService->saveEntityWithAttributes($shelf1, [
            'shelf_name' => 'Dãy kệ A1-01',
            'shelf_code' => 'SHELF-A1-01',
            'shelf_levels' => 5
        ]);

        $shelf2 = Entity::create([
            'entity_type_id' => $shelfType->entity_type_id,
            'parent_id' => $area1->entity_id,
            'entity_code' => 'SHELF-A1-02',
            'entity_name' => 'Dãy kệ A1-02',
            'is_active' => true
        ]);

        $eavService->saveEntityWithAttributes($shelf2, [
            'shelf_name' => 'Dãy kệ A1-02',
            'shelf_code' => 'SHELF-A1-02',
            'shelf_levels' => 4
        ]);
    }
}
