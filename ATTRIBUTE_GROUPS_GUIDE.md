# Attribute Groups Management Guide

## Tổng quan

Attribute Groups cho phép bạn tổ chức các attributes thành các nhóm (tabs) để dễ quản lý và hiển thị trong forms. Thay vì hiển thị tất cả attributes trong một form dài, bạn có thể chia chúng thành các tabs như "General Information", "Technical Details", "Media & Files", etc.

## Cấu trúc Attribute Groups

### Database Schema
```sql
CREATE TABLE attribute_groups (
    group_id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type_id INT NOT NULL,
    group_code VARCHAR(100) NOT NULL,
    group_name VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_type_group (entity_type_id, group_code),
    FOREIGN KEY fk_group_type (entity_type_id) REFERENCES entity_types(entity_type_id)
);
```

### Model Relationships
- **AttributeGroup** belongs to **EntityType**
- **AttributeGroup** has many **Attributes**
- **Attribute** belongs to **AttributeGroup** (optional)

## Các tính năng chính

### 1. Quản lý Attribute Groups

#### Tạo Attribute Group
1. **Truy cập**: Menu → Attribute Groups → Create
2. **Điền thông tin**:
   - **Entity Type**: Chọn loại thực thể
   - **Group Code**: Mã code duy nhất (VD: "general", "technical")
   - **Group Name**: Tên hiển thị (VD: "General Information")
   - **Sort Order**: Thứ tự hiển thị
   - **Active**: Trạng thái hoạt động

#### Các Group Code phổ biến
```
general     - General Information
technical   - Technical Details  
media       - Media & Files
seo         - SEO Settings
advanced    - Advanced Settings
pricing     - Pricing Information
shipping    - Shipping Details
```

### 2. Gán Attributes vào Groups

#### Cách 1: Trong Attribute Creation
1. Tạo attribute mới
2. Chọn **Group** trong dropdown
3. Attribute sẽ được gán vào group đó

#### Cách 2: Edit Attribute
1. Edit attribute hiện có
2. Thay đổi **Group** field
3. Save changes

### 3. Sử dụng trong Entity Types

#### Tạo Entity Type với Groups
1. **Truy cập**: Menu → Entity Types → Create
2. **Chọn Attribute Groups**: Checkbox các groups muốn sử dụng
3. **Chọn Attributes**: Chọn attributes cho từng group
4. **Submit**: Entity Type sẽ có cấu trúc tabs

## Ví dụ thực tế

### Ví dụ 1: Module Product với Groups

#### 1. Tạo Attribute Groups
```
General Information (general)
├── Product Name
├── Description
├── SKU
└── Status

Technical Details (technical)
├── Dimensions
├── Weight
├── Material
└── Specifications

Media & Files (media)
├── Main Image
├── Gallery Images
├── Manual PDF
└── Video

SEO Settings (seo)
├── Meta Title
├── Meta Description
├── Keywords
└── URL Slug
```

#### 2. Form hiển thị
```
┌─────────────────────────────────────┐
│ [General] [Technical] [Media] [SEO] │
├─────────────────────────────────────┤
│ General Information                 │
│ ┌─────────────────────────────────┐ │
│ │ Product Name: [____________]    │ │
│ │ Description: [____________]    │ │
│ │ SKU: [____________]            │ │
│ │ Status: [Dropdown]             │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### Ví dụ 2: Module Customer với Groups

#### 1. Tạo Attribute Groups
```
Personal Info (personal)
├── Full Name
├── Email
├── Phone
└── Date of Birth

Address Info (address)
├── Street Address
├── City
├── State
└── Postal Code

Account Settings (account)
├── Account Status
├── Registration Date
├── Last Login
└── Preferences
```

## API Endpoints

### 1. Attribute Groups CRUD
```
GET    /attribute-groups              - List groups
POST   /attribute-groups              - Create group
GET    /attribute-groups/{id}         - Show group
PUT    /attribute-groups/{id}          - Update group
DELETE /attribute-groups/{id}          - Delete group
```

### 2. API cho Dynamic Forms
```
GET /api/attribute-groups             - Get all groups
GET /api/attribute-groups/{entityTypeId} - Get groups by entity type
```

## JavaScript Integration

### 1. Load Attribute Groups
```javascript
function loadAttributeGroups() {
    fetch('/api/attribute-groups')
        .then(response => response.json())
        .then(groups => {
            // Render groups with checkboxes
            renderGroups(groups);
        });
}
```

### 2. Dynamic Form Generation
```javascript
function generateFormWithGroups(entityTypeId, attributes) {
    // Group attributes by group_id
    const groupedAttributes = groupAttributesByGroup(attributes);
    
    // Generate tabs
    generateTabs(groupedAttributes);
    
    // Generate form fields for each tab
    generateTabContent(groupedAttributes);
}
```

## Best Practices

### 1. Naming Convention
- **Group Code**: snake_case, descriptive (VD: "general_info", "technical_specs")
- **Group Name**: Human readable (VD: "General Information", "Technical Specifications")
- **Sort Order**: 10, 20, 30, 40... (để dễ thêm groups mới)

### 2. Group Organization
- **General**: Thông tin cơ bản, bắt buộc
- **Technical**: Chi tiết kỹ thuật
- **Media**: Files, images, documents
- **SEO**: Meta tags, URLs
- **Advanced**: Cài đặt nâng cao

### 3. Attribute Assignment
- Mỗi attribute chỉ thuộc 1 group
- Group có thể có 0 hoặc nhiều attributes
- Attributes không có group sẽ hiển thị trong tab "Other"

### 4. UI/UX Considerations
- Không quá nhiều tabs (tối đa 5-6 tabs)
- Tên tab ngắn gọn, dễ hiểu
- Sắp xếp theo thứ tự logic
- Responsive design cho mobile

## Troubleshooting

### 1. Groups không hiển thị
- Kiểm tra `is_active = true`
- Kiểm tra Entity Type có tồn tại không
- Kiểm tra JavaScript console errors

### 2. Attributes không được gán vào group
- Kiểm tra `group_id` trong attributes table
- Kiểm tra foreign key constraint
- Kiểm tra group có tồn tại không

### 3. Form không generate tabs
- Kiểm tra attributes có `group_id` không
- Kiểm tra JavaScript function `generateFormWithGroups`
- Kiểm tra CSS cho tabs

### 4. Performance Issues
- Sử dụng eager loading: `AttributeGroup::with(['attributes'])`
- Cache groups nếu không thay đổi thường xuyên
- Pagination cho large datasets

## Advanced Features

### 1. Conditional Groups
```javascript
// Show/hide groups based on conditions
function toggleGroup(groupId, show) {
    const groupElement = document.getElementById(`group-${groupId}`);
    groupElement.style.display = show ? 'block' : 'none';
}
```

### 2. Dynamic Group Creation
```javascript
// Create group on-the-fly
function createGroupFromAttributes(attributes) {
    const groupName = prompt('Enter group name:');
    if (groupName) {
        // Create group via API
        createGroup(groupName, attributes);
    }
}
```

### 3. Group Templates
```javascript
// Predefined group templates
const groupTemplates = {
    'ecommerce': ['general', 'pricing', 'inventory', 'seo'],
    'cms': ['content', 'media', 'seo', 'settings'],
    'crm': ['contact', 'address', 'communication', 'notes']
};
```

## Migration từ Non-Grouped

### 1. Existing Attributes
```sql
-- Add group_id column to attributes
ALTER TABLE attributes ADD COLUMN group_id INT NULL;
ALTER TABLE attributes ADD FOREIGN KEY fk_attr_group (group_id) 
    REFERENCES attribute_groups(group_id) ON DELETE SET NULL;
```

### 2. Data Migration
```php
// Migrate existing attributes to groups
$attributes = Attribute::whereNull('group_id')->get();
foreach ($attributes as $attribute) {
    $group = AttributeGroup::where('entity_type_id', $attribute->entity_type_id)
        ->where('group_code', 'general')
        ->first();
    
    if ($group) {
        $attribute->update(['group_id' => $group->group_id]);
    }
}
```

## Conclusion

Attribute Groups là một tính năng mạnh mẽ để tổ chức và quản lý attributes trong hệ thống EAV. Nó giúp:

- **Tổ chức tốt hơn**: Chia attributes thành các nhóm logic
- **UX tốt hơn**: Forms với tabs thay vì scroll dài
- **Quản lý dễ hơn**: Dễ tìm và edit attributes
- **Scalability**: Hỗ trợ modules phức tạp

**Lưu ý**: Sử dụng groups một cách hợp lý, không tạo quá nhiều groups nhỏ sẽ làm phức tạp UI.
