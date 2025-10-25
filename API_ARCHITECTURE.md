# Kiến trúc API với Repository và Service Pattern

## Tổng quan

Dự án sử dụng **Repository Pattern** + **Service Layer Pattern** để tạo API mạnh mẽ, dễ bảo trì và tái sử dụng.

## Kiến trúc đầy đủ

```
API/Web Controller
    ↓
Service (Business Logic)
    ↓
Repository (Data Access)
    ↓
Model (Eloquent ORM)
    ↓
Database
```

## Các Repository và Service hiện có

### 1. AttributeRepository & AttributeService

#### Repository Methods
```php
$attributeRepository->getByEntityType($entityTypeId);
$attributeRepository->getWithFilters($filters);
$attributeRepository->searchAttributes($search);
```

#### Service Methods
```php
$attributeService->createAttribute($data);
$attributeService->updateAttribute($attribute, $data);
$attributeService->deleteAttribute($attribute);
$attributeService->getAttributesByEntityType($entityTypeId);
$attributeService->getAllAttributes($filters);
```

### 2. EntityTypeRepository & EntityTypeService

#### Repository Methods
```php
$entityTypeRepository->search($search);
$entityTypeRepository->getWithAttributesCount();
```

#### Service Methods
```php
$entityTypeService->create($data);
$entityTypeService->update($entityType, $data);
$entityTypeService->delete($entityType);
$entityTypeService->search($search);
$entityTypeService->getWithAttributesCount();
```

### 3. EntityRepository & EntityService

#### Repository Methods
```php
$entityRepository->getByType($entityTypeId, $perPage);
$entityRepository->getTree($entityTypeId, $parentId);
$entityRepository->searchEntities($search, $entityTypeId);
$entityRepository->getWithAttributes($entityId);
```

#### Service Methods
```php
$entityService->create($data);
$entityService->update($entity, $data);
$entityService->delete($entity);
$entityService->getByType($entityTypeId, $perPage);
$entityService->getTree($entityTypeId, $parentId);
$entityService->searchEntities($search, $entityTypeId);
$entityService->getWithAttributes($entityId);
```

### 4. EavService (Không có Repository riêng)

#### Service Methods
```php
$eavService->saveEntityWithAttributes($entity, $attributeData);
$eavService->moveEntity($entity, $newParentId);
$eavService->getEntityHierarchy($entityTypeId, $parentId);
$eavService->validateEntityAttributes($entity, $attributeData);
$eavService->searchEntities($search, $attributeValues);
```

## Sử dụng trong API

### Ví dụ API Controller

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttributeService;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    protected $attributeService;
    
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    
    /**
     * GET /api/attributes
     */
    public function index(Request $request)
    {
        $filters = [
            'entity_type_id' => $request->get('entity_type_id'),
            'search' => $request->get('search')
        ];
        
        $attributes = $this->attributeService->getAllAttributes($filters);
        
        return response()->json([
            'success' => true,
            'data' => $attributes
        ]);
    }
    
    /**
     * POST /api/attributes
     */
    public function store(Request $request)
    {
        try {
            $attribute = $this->attributeService->createAttribute($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $attribute
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * PUT /api/attributes/{id}
     */
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);
        
        try {
            $this->attributeService->updateAttribute($attribute, $request->all());
            
            return response()->json([
                'success' => true,
                'data' => $attribute->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * DELETE /api/attributes/{id}
     */
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        
        try {
            $this->attributeService->deleteAttribute($attribute);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
```

## Sử dụng trong Web Controller

### Ví dụ Web Controller

```php
namespace App\Http\Controllers;

use App\Services\EntityTypeService;
use Inertia\Inertia;

class EntityTypeController extends Controller
{
    protected $entityTypeService;
    
    public function __construct(EntityTypeService $entityTypeService)
    {
        $this->entityTypeService = $entityTypeService;
    }
    
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        if ($search) {
            $entityTypes = $this->entityTypeService->search($search);
        } else {
            $entityTypes = $this->entityTypeService->getWithAttributesCount();
        }
        
        return Inertia::render('EntityTypes/Index', [
            'entityTypes' => $entityTypes
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $this->entityTypeService->create($request->all());
            
            return redirect()->route('entity-types.index')
                ->with('success', 'Đã tạo thành công');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
```

## Lợi ích

### 1. **Tái sử dụng code**
- Web và API dùng chung Service
- Không cần viết lại logic

### 2. **Dễ test**
- Mock Repository trong Service test
- Mock Service trong Controller test

### 3. **Dễ maintain**
- Logic nghiệp vụ tập trung trong Service
- Query tập trung trong Repository

### 4. **Dễ mở rộng**
- Thêm API endpoint mới dễ dàng
- Thêm logic mới không ảnh hưởng code cũ

## Tạo API mới

### Bước 1: Tạo Repository (nếu cần)

```bash
php artisan make:repository YourRepository
```

### Bước 2: Tạo Service

```bash
php artisan make:class Services/YourService
```

### Bước 3: Implement Service

```php
class YourService
{
    protected $repository;
    
    public function __construct(YourRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function getAll()
    {
        return $this->repository->all();
    }
}
```

### Bước 4: Tạo API Controller

```php
class Api\YourController extends Controller
{
    protected $yourService;
    
    public function __construct(YourService $yourService)
    {
        $this->yourService = $yourService;
    }
    
    public function index()
    {
        $data = $this->yourService->getAll();
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
```

## Best Practices

1. **Service chứa business logic**
2. **Repository chứa queries**
3. **Controller chỉ xử lý HTTP**
4. **Dependency Injection mọi nơi**
5. **Try-catch trong Controller, throw trong Service**
6. **Transaction trong Service**
7. **Return clean data từ Service**

## Testing

### Test Service

```php
use Mockery;
use App\Services\AttributeService;
use App\Repositories\AttributeRepository;

class AttributeServiceTest extends TestCase
{
    public function testCreateAttribute()
    {
        // Mock Repository
        $repository = Mockery::mock(AttributeRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->andReturn(new Attribute());
        
        // Test Service
        $service = new AttributeService($repository);
        $result = $service->createAttribute(['name' => 'Test']);
        
        $this->assertInstanceOf(Attribute::class, $result);
    }
}
```

### Test API

```php
use App\Services\AttributeService;

class AttributeApiTest extends TestCase
{
    public function testApiStore()
    {
        // Mock Service
        $service = Mockery::mock(AttributeService::class);
        $service->shouldReceive('createAttribute')
            ->once()
            ->andReturn(new Attribute(['name' => 'Test']));
        
        $this->app->instance(AttributeService::class, $service);
        
        // Test API
        $response = $this->json('POST', '/api/attributes', [
            'attribute_code' => 'test',
            'attribute_label' => 'Test'
        ]);
        
        $response->assertStatus(201)
                 ->assertJson(['success' => true]);
    }
}
```

## Kết luận

Với kiến trúc này:
- ✅ Code dễ đọc và maintain
- ✅ Dễ test và debug
- ✅ Dễ mở rộng và tái sử dụng
- ✅ Đáp ứng SOLID principles
- ✅ Sẵn sàng cho microservices

