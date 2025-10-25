<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EntityTypeRepository
 *
 * @package namespace App\Repositories;
 */
interface EntityTypeRepository extends RepositoryInterface
{
    /**
     * Get entity types with search
     */
    public function search($search);
    
    /**
     * Get entity types with attributes count
     */
    public function getWithAttributesCount();
}
