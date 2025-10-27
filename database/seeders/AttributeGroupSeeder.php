<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttributeGroup;
use App\Models\EntityType;

class AttributeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get entity types
        $entityTypes = EntityType::all();
        
        if ($entityTypes->isEmpty()) {
            $this->command->warn('No entity types found. Please run EntityTypeSeeder first.');
            return;
        }

        // Common attribute groups
        $commonGroups = [
            [
                'group_code' => 'general',
                'group_name' => 'General Information',
                'sort_order' => 10,
                'description' => 'Basic information and general details'
            ],
            [
                'group_code' => 'technical',
                'group_name' => 'Technical Details',
                'sort_order' => 20,
                'description' => 'Technical specifications and details'
            ],
            [
                'group_code' => 'media',
                'group_name' => 'Media & Files',
                'sort_order' => 30,
                'description' => 'Images, documents and media files'
            ],
            [
                'group_code' => 'seo',
                'group_name' => 'SEO Settings',
                'sort_order' => 40,
                'description' => 'Search engine optimization settings'
            ],
            [
                'group_code' => 'advanced',
                'group_name' => 'Advanced Settings',
                'sort_order' => 50,
                'description' => 'Advanced configuration options'
            ]
        ];

        foreach ($entityTypes as $entityType) {
            foreach ($commonGroups as $groupData) {
                AttributeGroup::create([
                    'entity_type_id' => $entityType->entity_type_id,
                    'group_code' => $groupData['group_code'],
                    'group_name' => $groupData['group_name'],
                    'sort_order' => $groupData['sort_order'],
                    'is_active' => true
                ]);
            }
        }

        $this->command->info('Attribute groups created successfully!');
    }
}