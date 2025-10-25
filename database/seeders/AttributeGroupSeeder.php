<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttributeGroup;

class AttributeGroupSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $groups = [
            [
                'entity_type_id' => null,
                'group_code' => 'basic',
                'group_name' => 'Basic Information',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'entity_type_id' => null,
                'group_code' => 'advanced',
                'group_name' => 'Advanced Settings',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'entity_type_id' => null,
                'group_code' => 'metadata',
                'group_name' => 'Metadata',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            AttributeGroup::create($group);
        }
    }
}