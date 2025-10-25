<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entity;
use App\Models\EntityType;
use App\Services\EavService;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $customerType = EntityType::where('type_code', 'customer')->first();
        
        if ($customerType) {
            // Create sample customer entity
            $entity = Entity::create([
                'entity_type_id' => $customerType->entity_type_id,
                'entity_code' => 'KH-001',
                'entity_name' => 'Nguyễn Văn A',
                'is_active' => true
            ]);

            $attributeData = [
                'customer_name' => 'Nguyễn Văn A',
                'customer_code' => 'KH-001',
                'birth_date' => '1990-01-15',
                'email' => 'nguyenvana@email.com',
                'phone' => '0123456789',
                'address' => '123 Đường ABC, Quận 1, TP.HCM'
            ];

            $eavService = new EavService();
            $eavService->saveEntityWithAttributes($entity, $attributeData);
        }
    }
}