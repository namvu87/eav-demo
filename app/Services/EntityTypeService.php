<?php

namespace App\Services;

use App\Models\EntityType;
use App\Repositories\EntityTypeRepository;
use Illuminate\Support\Facades\DB;

class EntityTypeService
{
    protected $repository;

    public function __construct(EntityTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new entity type
     */
    public function create(array $data): EntityType
    {
        DB::beginTransaction();
        
        try {
            $entityType = $this->repository->create($data);
            
            DB::commit();
            return $entityType;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update entity type
     */
    public function update(EntityType $entityType, array $data): EntityType
    {
        DB::beginTransaction();
        
        try {
            $this->repository->update($data, $entityType->entity_type_id);
            
            DB::commit();
            return $entityType->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete entity type
     */
    public function delete(EntityType $entityType): bool
    {
        DB::beginTransaction();
        
        try {
            // Check if has entities
            if ($entityType->entities()->count() > 0) {
                throw new \Exception('Không thể xóa loại thực thể có thực thể hiện tại.');
            }
            
            $this->repository->delete($entityType->entity_type_id);
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Search entity types
     */
    public function search($search)
    {
        return $this->repository->search($search);
    }

    /**
     * Get entity types with attributes count
     */
    public function getWithAttributesCount()
    {
        return $this->repository->getWithAttributesCount();
    }
}
