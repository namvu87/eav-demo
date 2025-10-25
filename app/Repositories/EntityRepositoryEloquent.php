<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Entity;

/**
 * Class EntityRepositoryEloquent
 *
 * @package namespace App\Repositories;
 */
class EntityRepositoryEloquent extends BaseRepository implements EntityRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Entity::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * Get entities by type with pagination
     */
    public function getByType($entityTypeId, $perPage = 20)
    {
        return $this->model
            ->where('entity_type_id', $entityTypeId)
            ->with(['parent', 'entityType'])
            ->orderBy('sort_order')
            ->orderBy('entity_name')
            ->paginate($perPage);
    }
    
    /**
     * Get entity tree (hierarchy)
     */
    public function getTree($entityTypeId = null, $parentId = null)
    {
        $query = $this->model->with(['entityType', 'children' => function($query) {
            $query->with(['entityType', 'children'])->orderBy('sort_order')->orderBy('entity_name');
        }])
        ->orderBy('sort_order')
        ->orderBy('entity_name');

        if ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId);
        }

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }
    
    /**
     * Search entities
     */
    public function searchEntities($search, $entityTypeId = null)
    {
        $query = $this->model->where('entity_name', 'like', "%{$search}%")
            ->orWhere('entity_code', 'like', "%{$search}%");
            
        if ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId);
        }
        
        return $query->with(['entityType', 'parent'])
            ->orderBy('entity_name')
            ->get();
    }
    
    /**
     * Get entity with all its attributes
     */
    public function getWithAttributes($entityId)
    {
        return $this->model
            ->with(['entityType', 'parent', 'children', 'entityType.attributes'])
            ->findOrFail($entityId);
    }
}
