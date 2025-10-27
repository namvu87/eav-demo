# Hierarchy Layout - Hướng dẫn sử dụng

## Tổng quan

Hierarchy layout cho phép bạn quản lý cấu trúc phân cấp của các entities trong hệ thống EAV. Bạn có thể tạo các entities có quan hệ cha-con và xem chúng dưới dạng cây phân cấp.

## Các tính năng chính

### 1. Xem Hierarchy Tree
- **URL**: `/hierarchy`
- **Mô tả**: Hiển thị tất cả entities dưới dạng cây phân cấp
- **Tính năng**:
  - Expand/collapse các node con
  - Filter theo entity type
  - Hiển thị level và số lượng children
  - Actions cho mỗi entity (view, edit, delete, move, add child)

### 2. Tạo Root Entity
- **URL**: `/hierarchy/create`
- **Mô tả**: Tạo entity gốc (không có parent)
- **Tính năng**:
  - Chọn entity type
  - Dynamic form generation dựa trên attributes
  - Validation đầy đủ

### 3. Tạo Child Entity
- **URL**: `/hierarchy/create?parent_id=X`
- **Mô tả**: Tạo entity con dưới một entity cha
- **Tính năng**:
  - Parent được tự động set
  - Dynamic form generation
  - Hierarchy path được tự động tính toán

### 4. Di chuyển Entity
- **Tính năng**: Modal để di chuyển entity sang parent khác
- **Validation**: Không thể di chuyển entity vào chính nó hoặc con của nó

### 5. Xóa Entity
- **Validation**: Chỉ có thể xóa entity không có children
- **Cascade**: Xóa entity sẽ xóa tất cả children

## Cấu trúc Files

```
resources/views/hierarchy/
├── index.blade.php              # Trang chính hiển thị hierarchy tree
├── create.blade.php             # Form tạo entity mới
└── partials/
    └── entity-node.blade.php    # Component hiển thị một entity node
```

## Cách sử dụng

### 1. Truy cập Hierarchy
```
http://localhost:8000/hierarchy
```

### 2. Tạo Root Entity
1. Click "Add Root Entity"
2. Chọn Entity Type
3. Điền thông tin cơ bản
4. Điền attributes (nếu có)
5. Submit

### 3. Tạo Child Entity
1. Click nút "+" bên cạnh entity cha
2. Chọn Entity Type cho child
3. Điền thông tin
4. Parent ID được tự động set
5. Submit

### 4. Quản lý Entity
- **View**: Click icon mắt để xem chi tiết
- **Edit**: Click icon bút để chỉnh sửa
- **Move**: Click icon mũi tên để di chuyển
- **Delete**: Click icon thùng rác để xóa
- **Add Child**: Click icon "+" để thêm con

## JavaScript Features

### 1. Toggle Children
```javascript
function toggleChildren(entityId)
```
- Expand/collapse children của một entity
- Thay đổi icon mũi tên

### 2. Move Modal
```javascript
function openMoveModal(entityId)
function closeMoveModal()
```
- Mở/đóng modal di chuyển entity
- Load danh sách parent có thể chọn

### 3. Dynamic Attributes
```javascript
function updateAttributes()
function generateAttributeField(attribute)
```
- Load attributes dựa trên entity type
- Generate form fields động
- Support các loại input: text, textarea, select, yesno, file

## API Endpoints

### 1. Get Attributes
```
GET /api/entity-types/{id}/attributes
```
- Trả về attributes của một entity type
- Bao gồm options cho select fields

### 2. Get Tree
```
GET /hierarchy/tree?entity_type_id=X
```
- Trả về hierarchy tree dưới dạng JSON
- Filter theo entity type

## Styling và UI

### 1. Responsive Design
- Mobile-friendly với sidebar collapse
- Grid layout responsive
- Touch-friendly buttons

### 2. Visual Hierarchy
- Indentation cho các level
- Color coding cho entity types
- Status badges (Active/Inactive)
- Level indicators

### 3. Interactive Elements
- Hover effects
- Loading states
- Confirmation dialogs
- Toast notifications

## Database Structure

### Entity Model Relationships
```php
// Entity has children
public function children()
{
    return $this->hasMany(Entity::class, 'parent_id');
}

// Entity belongs to parent
public function parent()
{
    return $this->belongsTo(Entity::class, 'parent_id');
}

// Entity belongs to entity type
public function entityType()
{
    return $this->belongsTo(EntityType::class, 'entity_type_id');
}
```

### Hierarchy Fields
- `parent_id`: ID của entity cha
- `level`: Mức độ trong hierarchy (0 = root)
- `path`: Đường dẫn đầy đủ từ root (ví dụ: /1/2/3/)
- `sort_order`: Thứ tự sắp xếp trong cùng level

## Best Practices

### 1. Entity Type Design
- Thiết kế entity types có ý nghĩa hierarchy
- Ví dụ: Warehouse → Zone → Area → Shelf

### 2. Naming Convention
- Sử dụng code có ý nghĩa
- Ví dụ: WH-001, ZONE-A, AREA-A1

### 3. Performance
- Sử dụng eager loading cho relationships
- Cache hierarchy tree nếu cần
- Pagination cho large datasets

### 4. Validation
- Không cho phép circular references
- Validate parent-child compatibility
- Check business rules trước khi move/delete

## Troubleshooting

### 1. Entity không hiển thị
- Kiểm tra `is_active = true`
- Kiểm tra entity type filter
- Kiểm tra parent_id relationship

### 2. Không thể tạo child
- Kiểm tra entity type có tồn tại
- Kiểm tra parent entity có active
- Kiểm tra validation rules

### 3. Move entity lỗi
- Kiểm tra không tạo circular reference
- Kiểm tra permissions
- Kiểm tra business rules

## Future Enhancements

### 1. Drag & Drop
- Kéo thả để move entities
- Visual feedback khi drag

### 2. Bulk Operations
- Select multiple entities
- Bulk move/delete operations

### 3. Advanced Filtering
- Filter theo multiple criteria
- Search trong hierarchy

### 4. Export/Import
- Export hierarchy to JSON/CSV
- Import từ external sources

### 5. Audit Trail
- Track changes to hierarchy
- Version history
- Rollback capabilities
