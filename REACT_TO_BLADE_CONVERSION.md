# Chuyển đổi từ React sang Blade Laravel

Dự án này đã được chuyển đổi từ React/Inertia.js sang Blade Laravel templates truyền thống.

## Các thay đổi chính

### 1. Layout và Navigation
- **File**: `resources/views/layouts/app.blade.php`
- **Mô tả**: Layout chính với sidebar navigation và responsive design
- **Tính năng**: 
  - Sidebar có thể thu gọn trên mobile
  - Navigation menu với icons
  - Flash messages support

### 2. Views đã chuyển đổi

#### Dashboard
- **File**: `resources/views/dashboard.blade.php`
- **Tính năng**: 
  - Statistics cards
  - Quick actions
  - Recent entities và entity types
  - System information

#### EAV Management
- **Index**: `resources/views/eav/index.blade.php`
  - Entity listing với pagination
  - Search và filtering
  - Actions (view, edit, delete)
- **Create**: `resources/views/eav/create.blade.php`
  - Dynamic form generation dựa trên entity type
  - Attribute rendering với các loại input khác nhau
  - JavaScript để handle dynamic attributes

#### Entity Types
- **Index**: `resources/views/entity-types/index.blade.php`
  - Grid layout cho entity types
  - Search functionality
- **Create**: `resources/views/entity-types/create.blade.php`
  - Form tạo entity type mới
  - Attribute selection

### 3. Components có thể tái sử dụng

#### Alert Component
- **File**: `resources/views/components/alert.blade.php`
- **Sử dụng**: `@include('components.alert', ['type' => 'success', 'message' => 'Success message'])`
- **Types**: success, error, warning, info

#### Button Components
- **Button**: `resources/views/components/button.blade.php`
- **Button Link**: `resources/views/components/button-link.blade.php`
- **Variants**: primary, secondary, success, danger, warning, outline, link
- **Sizes**: sm, md, lg

#### Navigation
- **File**: `resources/views/components/navigation.blade.php`
- **Mô tả**: Navigation menu với icons và active states

### 4. Controllers đã cập nhật

#### DashboardController
- Loại bỏ Inertia dependency
- Return Blade views với data

#### EavController
- Cập nhật tất cả methods để return Blade views
- Giữ nguyên logic xử lý data

#### EntityTypeController
- Chuyển đổi từ Inertia sang Blade
- Cập nhật data structure cho views

### 5. JavaScript Features

#### Dynamic Form Generation
- **File**: `resources/views/eav/create.blade.php`
- **Tính năng**: 
  - Load attributes dựa trên entity type selection
  - Render các loại input khác nhau (text, textarea, select, multiselect, yesno, file)
  - Handle validation errors

#### Sidebar Toggle
- **File**: `resources/views/layouts/app.blade.php`
- **Tính năng**: Mobile sidebar toggle functionality

## Cách sử dụng

### 1. Chạy ứng dụng
```bash
php artisan serve
```

### 2. Truy cập các routes
- Dashboard: `/`
- Entity Types: `/entity-types`
- EAV Entities: `/eav`
- Attributes: `/attributes`

### 3. Tạo Entity Type mới
1. Vào `/entity-types/create`
2. Điền thông tin cơ bản
3. Chọn attributes (nếu có)
4. Submit form

### 4. Tạo Entity mới
1. Vào `/eav/create`
2. Chọn Entity Type
3. Điền thông tin cơ bản
4. Điền các attributes động (sẽ load tự động)
5. Submit form

## Cấu trúc thư mục

```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout
├── components/
│   ├── alert.blade.php        # Alert component
│   ├── button.blade.php       # Button component
│   ├── button-link.blade.php  # Button link component
│   └── navigation.blade.php   # Navigation component
├── dashboard.blade.php        # Dashboard view
├── eav/
│   ├── index.blade.php        # EAV entities listing
│   └── create.blade.php       # Create entity form
└── entity-types/
    ├── index.blade.php        # Entity types listing
    └── create.blade.php      # Create entity type form
```

## Lưu ý

1. **CSS Framework**: Sử dụng Tailwind CSS qua CDN
2. **Icons**: Sử dụng Heroicons SVG icons
3. **JavaScript**: Vanilla JavaScript, không cần framework
4. **Responsive**: Mobile-first design với responsive breakpoints
5. **Flash Messages**: Tự động hiển thị success/error messages
6. **Form Validation**: Server-side validation với error display

## Tính năng đã giữ nguyên

- Dynamic form generation
- EAV system logic
- Database relationships
- Validation rules
- File upload support
- Search và filtering
- Pagination
- CRUD operations

## Cải tiến so với React version

1. **Performance**: Không cần JavaScript bundle
2. **SEO**: Server-side rendering
3. **Simplicity**: Ít dependencies hơn
4. **Maintenance**: Dễ maintain hơn với Blade templates
5. **Caching**: Có thể cache views
6. **Debugging**: Dễ debug hơn với Laravel tools
