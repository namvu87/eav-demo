<?php

namespace App\Services;

use App\Models\Entity;
use App\Repositories\EntityRepository;
use Illuminate\Support\Facades\DB;

class EntityService
{
    protected $repository;
    protected $eavService;

    public function __construct(EntityRepository $repository, EavService $eavService)
    {
        $this->repository = $repository;
        $this->eavService = $eavService;
    }

    /**
     * Create a new entity
     */
    public function create(array $data): Entity
    {
        DB::beginTransaction();
        
        try {
            $entity = new Entity($data);
            $attributeData = $data['attributes'] ?? [];
            
            $this->eavService->saveEntityWithAttributes($entity, $attributeData);
            
            DB::commit();
            return $entity;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update entity
     */
    public function update(Entity $entity, array $data): Entity
    {
        DB::beginTransaction();
        
        try {
            $entity->fill($data);
            $attributeData = $data['attributes'] ?? [];
            
            $this->eavService->saveEntityWithAttributes($entity, $attributeData);
            
            DB::commit();
            return $entity;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete entity
     */
    public function delete(Entity $entity): bool
    {
        DB::beginTransaction();
        
        try {
            // Check if has children
            if ($entity->children()->count() > 0) {
                throw new \Exception('Không thể xóa thực thể có con.');
            }
            
            $this->repository->delete($entity->entity_id);
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get entities by type
     */
    public function getByType($entityTypeId, $perPage = 20)
    {
        return $this->repository->getByType($entityTypeId, $perPage);
    }

    /**
     * Get entity tree
     */
    public function getTree($entityTypeId = null, $parentId = null)
    {
        return $this->repository->getTree($entityTypeId, $parentId);
    }

    /**
     * Search entities
     */
    public function searchEntities($search, $entityTypeId = null)
    {
        return $this->repository->searchEntities($search, $entityTypeId);
    }

    /**
     * Get entity with attributes
     */
    public function getWithAttributes($entityId)
    {
        return $this->repository->getWithAttributes($entityId);
    }
}
