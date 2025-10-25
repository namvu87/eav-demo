<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AttributeRepository
 *
 * @package namespace App\Repositories;
 */
interface AttributeRepository extends RepositoryInterface
{
    /**
     * Get attributes by entity type
     */
    public function getByEntityType($entityTypeId);
    
    /**
     * Get attributes with filters
     */
    public function getWithFilters(array $filters = []);
    
    /**
     * Search attributes
     */
    public function searchAttributes($search);
}
