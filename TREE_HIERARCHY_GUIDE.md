# 📚 Hướng dẫn sử dụng Tree Hierarchy - Phân cấp cây

## 🎯 Tổng quan

Hệ thống EAV động của bạn đã được bổ sung đầy đủ tính năng **Tree Hierarchy (Phân cấp cây)** theo nghiệp vụ 5 trong file `eav5.md`.

### ✨ Tính năng đã implement:

1. ✅ **Tree View** - Xem cây phân cấp đầy đủ
2. ✅ **Move Entity** - Di chuyển entity trong cây
3. ✅ **Breadcrumb/Ancestors** - Hiển thị đường dẫn phân cấp
4. ✅ **Tree Indentation** - Hiển thị thụt đầu dòng trong bảng
5. ✅ **Helper Methods** - Đầy đủ các phương thức hỗ trợ tree

---

## 🚀 Sử dụng trong Filament Admin

### 1. Tree View Page

**Truy cập:** `Entities → Tree View`

Trang này hiển thị:
- 📊 **Statistics Cards**: Tổng số entities, root entities, max level
- 🌳 **Tree Structure**: Cấu trúc cây đầy đủ với icon, màu sắc
- 🔍 **Type Filter**: Lọc theo Entity Type

**Tính năng:**
```
├── Hospital (HS-001)
│   ├── Department (DP-001)
│   │   ├── Room (RM-001)
│   │   └── Room (RM-002)
│   └── Department (DP-002)
└── Hospital (HS-002)
```

### 2. Move Entity Action

**Cách sử dụng:**
1. Vào danh sách Entities
2. Click vào icon **Move** (⇄) ở hàng entity cần di chuyển
3. Chọn parent mới trong dropdown
4. Confirm

**Validation:**
- ❌ Không thể di chuyển entity vào chính nó
- ❌ Không thể di chuyển entity vào con/cháu của nó
- ✅ Có thể di chuyển về root (chọn "No Parent")

**Kết quả:**
- `path` và `level` được tự động cập nhật
- Tất cả descendants cũng được cập nhật theo

### 3. Breadcrumb trong View Entity

**Truy cập:** `View Entity → Hierarchy Path section`

Hiển thị:
```
📍 Root → Parent → Grandparent → Current Entity
```

**Tính năng:**
- Click vào bất kỳ ancestor nào để xem chi tiết
- Hiển thị level của từng entity
- Current entity được highlight

### 4. Tree Indentation trong Table

**Danh sách Entities** tự động hiển thị:
```
Hospital A
    └─ Department X
        └─ Room 101
    └─ Department Y
Hospital B
```

**Sorting:** Mặc định sort theo `path` để hiển thị đúng cấu trúc cây

### 5. Tabs theo Entity Type

List page có tabs:
- **All Entities**: Tất cả
- **Root Only**: Chỉ root entities
- **[Type Name]**: Tab riêng cho từng entity type (Hospital, Department...)

---

## 💻 Sử dụng trong Code

### EavService Methods

```php
use App\Services\EavService;

$eavService = app(EavService::class);
$entity = Entity::find(1);
```

#### 1. Get Ancestors (Breadcrumb)

```php
// Lấy tất cả ancestors
$ancestors = $eavService->getAncestors($entity);
// Returns: Collection [root, parent, grandparent, ...]

// Lấy breadcrumb string
$breadcrumb = $eavService->getBreadcrumbString($entity);
// Returns: "Hospital A → Department X → Room 101"

// Custom separator
$breadcrumb = $eavService->getBreadcrumbString($entity, ' > ');
// Returns: "Hospital A > Department X > Room 101"
```

#### 2. Get Descendants

```php
// Lấy tất cả descendants (recursive)
$descendants = $eavService->getDescendants($entity);

// Lấy chỉ direct children
$children = $eavService->getChildren($entity);
```

#### 3. Get Tree Structure

```php
// Lấy tree cho entity type
$tree = $eavService->getTree($entityTypeId);

// Lấy tree từ một root cụ thể
$tree = $eavService->getTree($entityTypeId, $rootEntityId);
```

Tree structure trả về:
```php
[
    [
        'entity_id' => 1,
        'entity_code' => 'HS-001',
        'entity_name' => 'Hospital A',
        'level' => 0,
        'children' => [
            [
                'entity_id' => 2,
                'entity_code' => 'DP-001',
                'entity_name' => 'Department X',
                'level' => 1,
                'children' => [...]
            ]
        ]
    ]
]
```

#### 4. Move Entity

```php
// Di chuyển entity sang parent mới
$success = $eavService->moveEntity($entity, $newParentId);

// Di chuyển về root
$success = $eavService->moveEntity($entity, null);
```

**Lưu ý:**
- Tự động update `path`, `level` cho entity và descendants
- Validate không cho di chuyển vào descendant của chính nó
- Sử dụng Transaction để đảm bảo data integrity

#### 5. Get Tree Statistics

```php
$stats = $eavService->getTreeStats($entityTypeId);

// Returns:
[
    'total_entities' => 50,
    'root_entities' => 5,
    'max_level' => 4,
    'entities_by_level' => [
        0 => 5,   // 5 root entities
        1 => 15,  // 15 level-1 entities
        2 => 20,  // 20 level-2 entities
        3 => 8,   // 8 level-3 entities
        4 => 2    // 2 level-4 entities
    ]
]
```

---

## 🔧 Entity Model Methods

### Built-in Methods

```php
$entity = Entity::find(1);

// Get ancestors
$ancestors = $entity->getAncestors();

// Get descendants
$descendants = $entity->getDescendants();

// Relationships
$parent = $entity->parent;
$children = $entity->children;
```

### Scopes

```php
// Chỉ root entities
$roots = Entity::roots()->get();

// Entities theo type
$hospitals = Entity::ofType($hospitalTypeId)->get();

// Active entities
$active = Entity::active()->get();
```

---

## 📊 Database Structure

### Entities Table

| Field | Type | Mô tả |
|-------|------|-------|
| `entity_id` | INT | Primary key |
| `parent_id` | INT | Foreign key to entities (nullable) |
| `path` | VARCHAR(1000) | Materialized path: `/1/5/12/` |
| `level` | INT | Depth: 0=root, 1=level1... |

### Materialized Path Example

```
Hospital (id=1):     path = '/1/'           level = 0
  Department (id=5): path = '/1/5/'        level = 1
    Room (id=12):    path = '/1/5/12/'     level = 2
      Bed (id=25):   path = '/1/5/12/25/'  level = 3
```

### Queries

**Lấy tất cả descendants:**
```sql
SELECT * FROM entities 
WHERE path LIKE '/1/5/%'
AND entity_id != 5
ORDER BY path;
```

**Lấy ancestors:**
```sql
SELECT e.*
FROM entities target
JOIN entities e ON target.path LIKE CONCAT(e.path, '%')
WHERE target.entity_id = 25
ORDER BY e.level;
```

---

## 🎨 UI Components

### Tree Node Display

File: `resources/views/filament/resources/entity-resource/pages/tree-node.blade.php`

**Customization:**
- Sửa màu sắc, font, spacing trong section `<style>`
- Thêm actions (edit, delete) vào mỗi node
- Thay đổi tree lines character

### Ancestors List Display

File: `resources/views/filament/resources/entity-resource/pages/ancestors-list.blade.php`

**Customization:**
- Sửa separator (mặc định là →)
- Thay đổi badge colors
- Thêm thông tin hiển thị

---

## 📝 Best Practices

### 1. Khi tạo Entity mới

```php
// GOOD: Set parent_id, path và level được tự động tính
$entity = new Entity([
    'entity_type_id' => 1,
    'entity_code' => 'HS-001',
    'entity_name' => 'Hospital A',
    'parent_id' => null, // Root entity
]);

// EavService sẽ tự động set:
// - level = 0
// - path = '/1/'
```

### 2. Khi di chuyển Entity

```php
// GOOD: Sử dụng EavService
$eavService->moveEntity($entity, $newParentId);

// BAD: Không nên update trực tiếp
$entity->parent_id = $newParentId; // ❌ path và level sẽ không đúng
$entity->save();
```

### 3. Query Performance

```php
// GOOD: Sử dụng path để query descendants
$descendants = Entity::where('path', 'like', $entity->path . '%')->get();

// BAD: Recursive query (chậm)
function getDescendants($entity) {
    $children = $entity->children;
    foreach ($children as $child) {
        $descendants[] = getDescendants($child); // ❌ N+1 queries
    }
}
```

### 4. Validation

```php
// Luôn validate trước khi move
try {
    $eavService->moveEntity($entity, $newParentId);
} catch (\Exception $e) {
    // Handle: "Cannot move entity to its own descendant"
}
```

---

## 🐛 Troubleshooting

### Path không đúng

**Nguyên nhân:** Update entity không qua EavService

**Giải pháp:**
```php
// Recalculate paths
$entity->refresh();
if ($entity->parent_id) {
    $parent = Entity::find($entity->parent_id);
    $entity->path = $parent->path . $entity->entity_id . '/';
    $entity->level = $parent->level + 1;
} else {
    $entity->path = '/' . $entity->entity_id . '/';
    $entity->level = 0;
}
$entity->save();
```

### Tree View không hiển thị

**Kiểm tra:**
1. Entity có `is_active = 1`?
2. `path` có null không?
3. `entity_type_id` có đúng không?

### Move Entity bị lỗi

**Các lỗi thường gặp:**
- "Cannot move to descendant": Đang cố move vào con/cháu
- "New parent not found": parent_id không tồn tại
- "Cannot move to itself": source = target

---

## 🚀 Advanced Usage

### Custom Tree Rendering

```php
// Trong Livewire component
public function renderCustomTree($entityTypeId)
{
    $entities = Entity::where('entity_type_id', $entityTypeId)
        ->orderBy('path')
        ->get();
    
    return view('custom-tree', [
        'tree' => $this->buildTree($entities)
    ]);
}

private function buildTree($entities)
{
    $grouped = $entities->groupBy('parent_id');
    $roots = $grouped->get(null, collect());
    
    return $roots->map(function ($entity) use ($grouped) {
        $entity->children_nodes = $this->attachChildren($entity, $grouped);
        return $entity;
    });
}
```

### Lazy Loading Children

```php
// Chỉ load children khi cần
public function loadChildren($entityId)
{
    return Entity::where('parent_id', $entityId)
        ->orderBy('sort_order')
        ->get();
}
```

### Export Tree Structure

```php
use Illuminate\Support\Facades\Storage;

public function exportTree($entityTypeId)
{
    $eavService = app(EavService::class);
    $tree = $eavService->getTree($entityTypeId);
    
    $output = $this->treeToText($tree);
    
    Storage::put("exports/tree-{$entityTypeId}.txt", $output);
}

private function treeToText($tree, $level = 0)
{
    $output = '';
    foreach ($tree as $node) {
        $indent = str_repeat('  ', $level);
        $output .= $indent . '└─ ' . $node['entity_code'] . ' - ' . $node['entity_name'] . "\n";
        
        if (!empty($node['children'])) {
            $output .= $this->treeToText($node['children'], $level + 1);
        }
    }
    return $output;
}
```

---

## 📚 Tài liệu tham khảo

- `eav5.md` - Section 6: Nghiệp vụ 5: Phân cấp cây
- `eav2.sql` - Database schema
- `app/Services/EavService.php` - Tree helper methods
- `app/Models/Entity.php` - Model với tree relationships

---

## ✅ Checklist triển khai

- [x] Migration tables
- [x] Entity Model với tree methods
- [x] EavService tree helpers
- [x] Tree View page
- [x] Move Entity action
- [x] Breadcrumb display
- [x] Table tree indentation
- [x] Tabs theo entity type
- [x] Validation rules
- [x] UI components

---

**Tạo bởi:** Background Agent  
**Ngày:** 2025-10-24  
**Dựa trên:** eav2.sql, eav5.md
