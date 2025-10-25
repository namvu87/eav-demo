<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EntityRepository
 *
 * @package namespace App\Repositories;
 */
interface EntityRepository extends RepositoryInterface
{
    /**
     * Get entities by type with pagination
     */
    public function getByType($entityTypeId, $perPage = 20);
    
    /**
     * Get entity tree (hierarchy)
     */
    public function getTree($entityTypeId = null, $parentId = null);
    
    /**
     * Search entities
     */
    public function searchEntities($search, $entityTypeId = null);
    
    /**
     * Get entity with all its attributes
     */
    public function getWithAttributes($entityId);
}
