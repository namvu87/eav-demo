# Repository Pattern với L5-Repository

## Tổng quan

Dự án sử dụng **Repository Pattern** kết hợp với `prettus/l5-repository` để tách biệt logic query khỏi Models và Controllers.

## Kiến trúc 3 lớp

```
Controller -> Service -> Repository -> Model -> Database
```

- **Controller**: Xử lý HTTP requests/responses
- **Service**: Chứa logic nghiệp vụ
- **Repository**: Xử lý database queries
- **Model**: Eloquent ORM

## Cấu trúc

```
app/
├── Repositories/
│   ├── AttributeRepository.php          # Interface
│   └── AttributeRepositoryEloquent.php  # Implementation
├── Services/
│   └── AttributeService.php             # Uses Repository
└── Http/
    └── Controllers/
        ├── AttributeController.php      # Web Controller
        └── Api/
            └── AttributeController.php  # API Controller
```

## Lợi ích

### 1. **Tách biệt query logic**
   - Queries tập trung trong Repository
   - Dễ maintain và test
   - Dễ tái sử dụng

### 2. **Tách biệt nghiệp vụ**
   - Service chứa business logic
   - Repository chỉ xử lý data access
   - Controller chỉ xử lý HTTP

### 3. **Dễ test**
   - Mock Repository trong Service test
   - Mock Service trong Controller test
   - Test độc lập từng lớp

### 4. **Dễ mở rộng**
   - Thay đổi query không ảnh hưởng Service
   - Thêm logic không ảnh hưởng Repository

## Tạo Repository

### 1. Tạo Interface

```php
namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

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
}
```

### 2. Tạo Implementation

```php
namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\Attribute;

class AttributeRepositoryEloquent extends BaseRepository implements AttributeRepository
{
    public function model()
    {
        return Attribute::class;
    }
    
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
    
    public function getWithFilters(array $filters = [])
    {
        $query = $this->model->with(['entityType', 'group', 'options']);
        
        if (isset($filters['entity_type_id'])) {
            $query->where('entity_type_id', $filters['entity_type_id']);
        }
        
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('attribute_code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('attribute_label', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('sort_order')->paginate(15);
    }
}
```

### 3. Inject vào Service

```php
namespace App\Services;

use App\Repositories\AttributeRepository;

class AttributeService
{
    protected $repository;
    
    public function __construct(AttributeRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function getAttributesByEntityType($entityTypeId)
    {
        // Use repository instead of direct model query
        return $this->repository->getByEntityType($entityTypeId);
    }
    
    public function getAllAttributes(array $filters = [])
    {
        return $this->repository->getWithFilters($filters);
    }
}
```

### 4. Use trong Controller

```php
namespace App\Http\Controllers;

use App\Services\AttributeService;

class AttributeController extends Controller
{
    protected $attributeService;
    
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    
    public function index(Request $request)
    {
        $filters = [
            'entity_type_id' => $request->get('entity_type_id'),
            'search' => $request->get('search')
        ];
        
        // Use service instead of direct repository query
        $attributes = $this->attributeService->getAllAttributes($filters);
        
        return Inertia::render('Attributes/Index', [
            'attributes' => $attributes
        ]);
    }
}
```

## Methods có sẵn từ L5-Repository

### Query Methods
```php
$repository->find($id);
$repository->findByField('field', 'value');
$repository->findWhere(['field' => 'value']);
$repository->all();
$repository->first();
$repository->count();
$repository->paginate(15);
```

### Create/Update/Delete
```php
$repository->create($attributes);
$repository->update($attributes, $id);
$repository->delete($id);
```

### Relationships
```php
$repository->with('relationship');
$repository->has('relationship');
```

### Criteria (Advanced Filtering)
```php
$repository->pushCriteria(new RequestCriteria);
$repository->pushCriteria(new OrderByCriteria);
```

## So sánh: Trước và Sau

### ❌ Trước (Direct Model Usage)

```php
// In Service
public function getAttributesByEntityType($entityTypeId)
{
    return Attribute::where('entity_type_id', $entityTypeId)
        ->with('options')
        ->orderBy('sort_order')
        ->get();
}

// In Controller
$attributes = Attribute::where('entity_type_id', $id)
    ->with('options')
    ->orderBy('sort_order')
    ->get();
```

### ✅ Sau (With Repository)

```php
// In Repository
public function getByEntityType($entityTypeId)
{
    return $this->model
        ->where('entity_type_id', $entityTypeId)
        ->with('options')
        ->orderBy('sort_order')
        ->get();
}

// In Service
public function getAttributesByEntityType($entityTypeId)
{
    return $this->repository->getByEntityType($entityTypeId);
}

// In Controller
$attributes = $this->attributeService->getAttributesByEntityType($id);
```

## Best Practices

1. **Repository chỉ chứa queries**, không có business logic
2. **Service chứa business logic**, sử dụng Repository để truy cập data
3. **Controller chỉ xử lý HTTP**, gọi Service
4. **Dependency Injection** tất cả dependencies
5. **Single Responsibility**: Mỗi class chỉ làm một việc

## Repository hiện có

### AttributeRepository
- `getByEntityType()` - Lấy attributes theo entity type
- `getWithFilters()` - Lấy attributes với filters
- `searchAttributes()` - Tìm kiếm attributes

## Tạo Repository mới

```bash
php artisan make:repository YourRepository
```

Sau đó:
1. Tạo methods trong Interface
2. Implement methods trong Eloquent class
3. Inject vào Service
4. Sử dụng trong Service methods

## Kết hợp với Service Layer

```
Controller (HTTP) 
    ↓
Service (Business Logic)
    ↓
Repository (Data Access)
    ↓
Model (ORM)
    ↓
Database
```

**Luồng xử lý:**
1. Controller nhận request
2. Controller gọi Service
3. Service thực hiện business logic
4. Service gọi Repository để lấy data
5. Repository query database
6. Return data ngược lại

## Kết luận

Repository Pattern giúp:
- ✅ Tách biệt layers rõ ràng
- ✅ Code dễ test
- ✅ Dễ maintain và mở rộng
- ✅ Tái sử dụng queries
- ✅ Đáp ứng SOLID principles
