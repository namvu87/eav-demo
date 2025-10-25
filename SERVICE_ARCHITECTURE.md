# Kiến trúc Service Layer

## Tổng quan

Dự án này sử dụng **Service Layer Pattern** để tách biệt logic nghiệp vụ khỏi Controllers, giúp code dễ bảo trì, test và tái sử dụng.

## Cấu trúc

```
app/
├── Services/
│   ├── EavService.php          # Service cho Entity-Attribute-Value
│   └── AttributeService.php    # Service cho Attributes
├── Http/
│   └── Controllers/
│       ├── AttributeController.php    # Web Controller
│       └── Api/
│           └── AttributeController.php # API Controller
```

## Lợi ích

### 1. **Tách biệt logic nghiệp vụ**
   - Controllers chỉ xử lý HTTP requests/responses
   - Services chứa tất cả logic nghiệp vụ
   - Dễ test và maintain

### 2. **Tái sử dụng code**
   - Web Controllers và API Controllers dùng chung Service
   - Không cần viết lại logic
   - Đảm bảo tính nhất quán

### 3. **Dễ mở rộng**
   - Thêm API mới chỉ cần tạo Controller mới
   - Tất cả đều dùng chung Service
   - Dễ tích hợp với các hệ thống khác

## Cách sử dụng

### Web Controller
```php
use App\Services\AttributeService;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [...]);
        
        // Use service
        $attribute = $this->attributeService->createAttribute($request->all());
        
        // Return response
        return redirect()->route('attributes.index')
            ->with('success', 'Thuộc tính đã được tạo thành công.');
    }
}
```

### API Controller
```php
use App\Services\AttributeService;

class Api\AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function store(Request $request)
    {
        try {
            // Use same service
            $attribute = $this->attributeService->createAttribute($request->all());
            
            // Return JSON
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
}
```

### Service
```php
class AttributeService
{
    public function createAttribute(array $data): Attribute
    {
        DB::beginTransaction();
        
        try {
            // Business logic here
            $attribute = Attribute::create($data);
            
            if (isset($data['options'])) {
                $this->saveOptions($attribute, $data['options']);
            }
            
            DB::commit();
            return $attribute;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

## Services hiện có

### EavService
- `saveEntityWithAttributes()` - Lưu entity với attributes
- `validateEntityAttributes()` - Validate attributes
- `moveEntity()` - Di chuyển entity trong hierarchy
- `getEntityHierarchy()` - Lấy cấu trúc cây

### AttributeService  
- `createAttribute()` - Tạo attribute mới
- `updateAttribute()` - Cập nhật attribute
- `deleteAttribute()` - Xóa attribute
- `getAttributesByEntityType()` - Lấy attributes theo entity type
- `getAllAttributes()` - Lấy tất cả attributes với filters

## Tạo Service mới

1. **Tạo file service:**
```bash
php artisan make:class Services/YourService
```

2. **Implement methods:**
```php
class YourService
{
    public function yourMethod(): ReturnType
    {
        // Business logic here
    }
}
```

3. **Inject vào Controller:**
```php
public function __construct(YourService $yourService)
{
    $this->yourService = $yourService;
}
```

4. **Sử dụng trong Controller:**
```php
public function action()
{
    $result = $this->yourService->yourMethod();
    return response()->json($result);
}
```

## Best Practices

1. **Mỗi Service chỉ làm một việc** (Single Responsibility)
2. **Sử dụng Dependency Injection** để inject services
3. **Handle transactions trong Services**, không phải Controllers
4. **Throw exceptions** thay vì return error codes
5. **Return clean data** (Models/Collections), không phải Responses

## API Endpoints

### Attributes API
- `POST /api/attributes` - Tạo attribute
- `GET /api/attributes/{id}` - Lấy attribute
- `PUT /api/attributes/{id}` - Cập nhật attribute
- `DELETE /api/attributes/{id}` - Xóa attribute
- `GET /api/entity-types/{typeId}/attributes` - Lấy attributes theo entity type

Tất cả đều sử dụng `AttributeService` chung!
