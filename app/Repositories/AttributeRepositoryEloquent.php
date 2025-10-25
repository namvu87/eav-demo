<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Attribute;
use App\Validators\AttributeRepositoryValidator;

/**
 * Class AttributeRepositoryEloquent
 *
 * @package namespace App\Repositories;
 */
class AttributeRepositoryEloquent extends BaseRepository implements AttributeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Attribute::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * Get attributes by entity type
     */
    public function getByEntityType($entityTypeId)
    {
        return $this->model
            ->with(['options' => function($query) {
                $query->orderBy('sort_order');
            }])
            ->where('entity_type_id', $entityTypeId)
            ->orderBy('sort_order')
            ->get();
    }
    
    /**
     * Get attributes with filters
     */
    public function getWithFilters(array $filters = [])
    {
        $query = $this->model->with(['entityType', 'group', 'options']);
        
        if (isset($filters['entity_type_id'])) {
            $query->where('entity_type_id', $filters['entity_type_id']);
        }
        
        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }
        
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('attribute_code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('attribute_label', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('sort_order')->paginate(15);
    }
    
    /**
     * Search attributes
     */
    public function searchAttributes($search)
    {
        return $this->model
            ->where('attribute_code', 'like', "%{$search}%")
            ->orWhere('attribute_label', 'like', "%{$search}%")
            ->get();
    }
}
