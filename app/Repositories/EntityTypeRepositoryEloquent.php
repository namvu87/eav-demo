<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\EntityType;

/**
 * Class EntityTypeRepositoryEloquent
 *
 * @package namespace App\Repositories;
 */
class EntityTypeRepositoryEloquent extends BaseRepository implements EntityTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return EntityType::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * Get entity types with search
     */
    public function search($search)
    {
        return $this->model
            ->with(['attributes' => function($query) {
                $query->orderBy('sort_order');
            }])
            ->where(function($query) use ($search) {
                $query->where('type_name', 'like', "%{$search}%")
                      ->orWhere('type_code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('type_name')
            ->get();
    }
    
    /**
     * Get entity types with attributes count
     */
    public function getWithAttributesCount()
    {
        return $this->model
            ->withCount('attributes')
            ->with('attributes')
            ->orderBy('type_name')
            ->get();
    }
}
