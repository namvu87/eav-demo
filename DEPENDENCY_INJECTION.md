# Dependency Injection trong Laravel

## Tổng quan

Laravel sử dụng **Service Container** để quản lý dependencies thông qua **Dependency Injection**. Để Repository và Service Pattern hoạt động đúng, cần đăng ký bindings trong `AppServiceProvider`.

## Bind Repository Interfaces

### Tại sao cần bind?

Khi inject `AttributeRepository` vào `AttributeService`, Laravel cần biết implementation nào sử dụng:
- `AttributeRepository` (Interface)
- `AttributeRepositoryEloquent` (Implementation)

### Cách bind trong AppServiceProvider

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Interface -> Implementation
        $this->app->bind(
            \App\Repositories\AttributeRepository::class,
            \App\Repositories\AttributeRepositoryEloquent::class
        );
        
        $this->app->bind(
            \App\Repositories\EntityTypeRepository::class,
            \App\Repositories\EntityTypeRepositoryEloquent::class
        );
        
        $this->app->bind(
            \App\Repositories\EntityRepository::class,
            \App\Repositories\EntityRepositoryEloquent::class
        );
    }
}
```

## Bind vs Singleton

### `bind()` - Tạo instance mới mỗi lần

```php
$this->app->bind(Interface::class, Implementation::class);
```

- Mỗi lần resolve sẽ tạo instance mới
- Phù hợp cho Repository (stateless)
- Dùng khi: State không quan trọng

### `singleton()` - Chỉ tạo 1 instance

```php
$this->app->singleton(Service::class);
```

- Chỉ tạo 1 instance duy nhất trong request
- Phù hợp cho Service (có state hoặc expensive operations)
- Dùng khi: Cần maintain state hoặc tối ưu performance

## Luồng hoạt động

### 1. Request đến Controller

```php
class AttributeController extends Controller
{
    protected $attributeService;
    
    public function __construct(AttributeService $attributeService)
    {
        // Laravel tự động resolve AttributeService
        $this->attributeService = $attributeService;
    }
}
```

### 2. Laravel resolve AttributeService

```php
// Laravel thấy constructor cần AttributeService
// Tìm trong Service Container
// Tạo instance AttributeService
$attributeService = new AttributeService(...);
```

### 3. Service cần Repository

```php
class AttributeService
{
    protected $repository;
    
    public function __construct(AttributeRepository $repository)
    {
        // Laravel tự động resolve AttributeRepository
        // Nhìn vào bindings, dùng AttributeRepositoryEloquent
        $this->repository = $repository;
    }
}
```

## Tự động resolve (Auto-wiring)

Laravel có thể **tự động resolve** dependencies nếu:
1. Có bind trong Service Provider, hoặc
2. Class không là Interface, hoặc
3. Interface có duy nhất 1 implementation

### Ví dụ: Không cần bind

```php
// Service không cần bind vì không phải Interface
class AttributeService
{
    protected $repository;
    
    public function __construct(AttributeRepository $repository)
    {
        // Laravel tự tìm AttributeRepositoryEloquent
        $this->repository = $repository;
    }
}
```

### Ví dụ: Cần bind

```php
// Interface CẦN bind
interface AttributeRepository extends RepositoryInterface {}

// Implementation
class AttributeRepositoryEloquent implements AttributeRepository {}

// Service cần bind để biết dùng implementation nào
class AttributeService
{
    public function __construct(AttributeRepository $repository) {}
}
```

## Best Practices

### 1. Bind Repository Interface

```php
public function register()
{
    // Interface -> Implementation
    $this->app->bind(
        AttributeRepository::class,
        AttributeRepositoryEloquent::class
    );
}
```

### 2. Singleton cho Service (tùy chọn)

```php
public function register()
{
    // Tạo 1 instance duy nhất cho Service
    $this->app->singleton(AttributeService::class);
}
```

### 3. Đăng ký trong AppServiceProvider

```php
namespace App\Providers;

use App\Repositories\AttributeRepository;
use App\Repositories\AttributeRepositoryEloquent;
use App\Services\AttributeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            AttributeRepository::class,
            AttributeRepositoryEloquent::class
        );
        
        // Service singletons (optional)
        $this->app->singleton(AttributeService::class);
    }
}
```

## Debug: Kiểm tra binding

### Check binding tồn tại

```php
// Trong tinker hoặc code
app()->bound(AttributeRepository::class); // true/false

// Check instance
app(AttributeRepository::class); // Get instance

// Check bindings
dd(app()->getBindings());
```

### Test trong Controller

```php
public function test()
{
    // Test resolve
    $repo = app(AttributeRepository::class);
    $service = app(AttributeService::class);
    
    dd([
        'repository' => get_class($repo),
        'service' => get_class($service)
    ]);
}
```

## Configurations đã thêm

### AppServiceProvider.php

```php
public function register(): void
{
    // Repository bindings
    $this->app->bind(
        \App\Repositories\AttributeRepository::class,
        \App\Repositories\AttributeRepositoryEloquent::class
    );
    
    $this->app->bind(
        \App\Repositories\EntityTypeRepository::class,
        \App\Repositories\EntityTypeRepositoryEloquent::class
    );
    
    $this->app->bind(
        \App\Repositories\EntityRepository::class,
        \App\Repositories\EntityRepositoryEloquent::class
    );
    
    // Service singletons (optional)
    $this->app->singleton(\App\Services\AttributeService::class);
    $this->app->singleton(\App\Services\EntityTypeService::class);
    $this->app->singleton(\App\Services\EntityService::class);
    $this->app->singleton(\App\Services\EavService::class);
}
```

## Lợi ích của DI

### 1. **Loose Coupling**
```php
// Good: Inject dependency
class AttributeService
{
    public function __construct(AttributeRepository $repository) {}
}

// Bad: Tight coupling
class AttributeService
{
    public function __construct()
    {
        $this->repository = new AttributeRepositoryEloquent();
    }
}
```

### 2. **Dễ test**
```php
// Mock repository trong test
$mockRepo = Mockery::mock(AttributeRepository::class);
$service = new AttributeService($mockRepo);
```

### 3. **Dễ swap implementation**
```php
// Chỉ cần đổi binding
$this->app->bind(
    AttributeRepository::class,
    NewAttributeRepository::class // New implementation
);
```

## Troubleshooting

### Lỗi: "Target [Interface] is not instantiable"

**Nguyên nhân:** Chưa bind Interface

**Giải pháp:** Thêm binding trong `AppServiceProvider`

```php
$this->app->bind(
    YourInterface::class,
    YourImplementation::class
);
```

### Lỗi: "ReflectionException: Class not found"

**Nguyên nhân:** Import sai hoặc namespace sai

**Giải pháp:** Check namespace trong bindings

```php
use App\Repositories\AttributeRepository;
use App\Repositories\AttributeRepositoryEloquent;

$this->app->bind(
    AttributeRepository::class, // Đầy đủ namespace
    AttributeRepositoryEloquent::class
);
```

### Controller không inject được Service

**Nguyên nhân:** Constructor chưa type-hint đúng

**Giải pháp:** 

```php
// Good
public function __construct(AttributeService $service)

// Bad
public function __construct()
{
    $this->service = new AttributeService(); // Không dùng DI
}
```

## Kết luận

- ✅ Repository Interface **PHẢI** bind với Implementation
- ✅ Service có thể bind singleton (optional)
- ✅ Laravel auto-resolve dependencies qua type-hint
- ✅ Dễ test và maintain
- ✅ Loose coupling, high cohesion
