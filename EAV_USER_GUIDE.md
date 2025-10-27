# EAV System - User Guide

## Tổng quan

Hệ thống EAV (Entity-Attribute-Value) cho phép bạn tạo các modules động mà không cần viết code database hay tạo tables mới. Bạn chỉ cần định nghĩa Entity Types và Attributes, hệ thống sẽ tự động tạo forms và xử lý dữ liệu.

## Các thành phần chính

### 1. Entity Types (Loại thực thể)
- Định nghĩa các loại dữ liệu (VD: Product, Customer, Order)
- Mỗi Entity Type có thể có nhiều Attributes
- Có thể có quan hệ parent-child

### 2. Attributes (Thuộc tính)
- Định nghĩa các trường dữ liệu (VD: name, price, email)
- Hỗ trợ nhiều loại input: text, number, select, file, date, etc.
- Có validation rules và constraints

### 3. Entities (Thực thể)
- Dữ liệu thực tế được lưu trữ
- Mỗi Entity thuộc về một Entity Type
- Có thể có quan hệ parent-child

## Hướng dẫn tạo EAV Module

### Bước 1: Tạo Entity Type

1. **Truy cập**: Menu → Entity Types → Create
2. **Điền thông tin cơ bản**:
   - **Type Name**: Tên loại thực thể (VD: "Sản phẩm")
   - **Type Code**: Mã code (VD: "product")
   - **Description**: Mô tả ngắn gọn
   - **Active**: ✓ (để kích hoạt)

3. **Chọn Attributes**:
   - Chọn từ danh sách attributes có sẵn
   - Hoặc tạo attributes mới bằng Quick Create

4. **Submit**: Click "Create Entity Type"

### Bước 2: Tạo Attributes (nếu cần)

#### Cách 1: Quick Create (Khuyến nghị)
1. **Trong Entity Type Create**: Click "Quick Create"
2. **Điền thông tin**:
   - **Attribute Label**: Tên hiển thị (VD: "Tên sản phẩm")
   - **Attribute Code**: Mã code (VD: "product_name")
   - **Input Type**: Chọn loại input
   - **Description**: Mô tả (optional)

3. **Cấu hình Validation** (tùy theo Input Type):
   - **Text**: Min/max length, regex pattern
   - **Number**: Min/max value, decimal places
   - **File**: Max count, extensions, file size
   - **Select**: Options list

4. **Submit**: Click "Create & Select"

#### Cách 2: Full Create
1. **Menu**: Attributes → Create
2. **Điền đầy đủ thông tin**
3. **Submit**

### Bước 3: Tạo Entities

1. **Truy cập**: Menu → Entities → Create
2. **Chọn Entity Type**: Dropdown hiển thị các Entity Types
3. **Điền form**: Form sẽ tự động generate dựa trên Attributes
4. **Submit**: Click "Create Entity"

## Ví dụ thực tế

### Ví dụ 1: Module Quản lý Sản phẩm

#### 1. Tạo Entity Type "Product"
```
Type Name: Sản phẩm
Type Code: product
Description: Quản lý thông tin sản phẩm
```

#### 2. Tạo Attributes
```
1. Tên sản phẩm (product_name) - Text - Required
2. Mô tả (description) - Textarea - Optional
3. Giá (price) - Number - Required - Min: 0, Decimal: 2
4. Danh mục (category) - Select - Required
   Options: Điện tử, Thời trang, Gia dụng
5. Hình ảnh (images) - File - Max: 5 files, Extensions: jpg,png
6. Trạng thái (status) - Select - Required
   Options: Active, Inactive, Draft
7. Ngày tạo (created_date) - Date - Required
```

#### 3. Sử dụng
- Tạo sản phẩm mới: Entities → Create → Chọn "Sản phẩm"
- Form sẽ tự động có các trường trên
- Validation tự động áp dụng

### Ví dụ 2: Module Quản lý Khách hàng

#### 1. Tạo Entity Type "Customer"
```
Type Name: Khách hàng
Type Code: customer
Description: Thông tin khách hàng
```

#### 2. Tạo Attributes
```
1. Họ tên (full_name) - Text - Required - Min: 2, Max: 100
2. Email (email) - Email - Required - Unique
3. Số điện thoại (phone) - Text - Required - Pattern: ^[0-9]{10}$
4. Địa chỉ (address) - Textarea - Optional
5. Ngày sinh (birth_date) - Date - Optional
6. Giới tính (gender) - Select - Required
   Options: Nam, Nữ, Khác
7. Trạng thái (status) - Select - Required
   Options: Active, Inactive, Blocked
```

### Ví dụ 3: Module Quản lý Đơn hàng với Hierarchy

#### 1. Tạo Entity Type "Order"
```
Type Name: Đơn hàng
Type Code: order
Description: Quản lý đơn hàng
```

#### 2. Tạo Attributes
```
1. Mã đơn hàng (order_code) - Text - Required - Unique
2. Khách hàng (customer_id) - Select - Required (link to Customer)
3. Ngày đặt (order_date) - Date - Required
4. Tổng tiền (total_amount) - Number - Required - Min: 0
5. Trạng thái (status) - Select - Required
   Options: Pending, Processing, Shipped, Delivered, Cancelled
6. Ghi chú (notes) - Textarea - Optional
```

#### 3. Tạo Entity Type "OrderItem" (Child)
```
Type Name: Chi tiết đơn hàng
Type Code: order_item
Description: Sản phẩm trong đơn hàng
Parent: Order
```

#### 4. Tạo Attributes cho OrderItem
```
1. Sản phẩm (product_id) - Select - Required (link to Product)
2. Số lượng (quantity) - Number - Required - Min: 1
3. Giá (price) - Number - Required - Min: 0
4. Thành tiền (subtotal) - Number - Required - Min: 0
```

## Các loại Input Types

### 1. Text
- **Mô tả**: Input text đơn giản
- **Validation**: Min/max length, regex pattern
- **Ví dụ**: Tên sản phẩm, mã code

### 2. Textarea
- **Mô tả**: Text area nhiều dòng
- **Validation**: Min/max length
- **Ví dụ**: Mô tả, ghi chú

### 3. Number
- **Mô tả**: Input số
- **Validation**: Min/max value, decimal places, step
- **Ví dụ**: Giá, số lượng, điểm số

### 4. Select
- **Mô tả**: Dropdown lựa chọn
- **Validation**: Required, options list
- **Ví dụ**: Danh mục, trạng thái, giới tính

### 5. Multiselect
- **Mô tả**: Chọn nhiều options
- **Validation**: Min/max selections
- **Ví dụ**: Tags, sở thích

### 6. Yes/No
- **Mô tả**: Checkbox boolean
- **Validation**: Required
- **Ví dụ**: Active, Featured, Published

### 7. File
- **Mô tả**: Upload file
- **Validation**: Max count, extensions, file size
- **Ví dụ**: Hình ảnh, tài liệu

### 8. Email
- **Mô tả**: Input email
- **Validation**: Email format, verification
- **Ví dụ**: Email khách hàng

### 9. URL
- **Mô tả**: Input URL
- **Validation**: URL format, protocol
- **Ví dụ**: Website, social links

### 10. Date
- **Mô tả**: Date picker
- **Validation**: Min/max date, future date
- **Ví dụ**: Ngày sinh, ngày tạo

### 11. DateTime
- **Mô tả**: DateTime picker
- **Validation**: Min/max datetime
- **Ví dụ**: Thời gian đặt hàng

## Hierarchy (Quan hệ Parent-Child)

### 1. Tạo Parent Entity Type
```
VD: Order (Đơn hàng)
```

### 2. Tạo Child Entity Type
```
VD: OrderItem (Chi tiết đơn hàng)
Parent: Order
```

### 3. Sử dụng Hierarchy
- **Tạo Parent**: Entities → Create → Chọn "Order"
- **Tạo Child**: Trong Order detail → Click "+" → Chọn "OrderItem"
- **Xem Hierarchy**: Menu → Hierarchy

## Best Practices

### 1. Naming Convention
- **Entity Type**: PascalCase (VD: "Product", "Customer")
- **Attribute Code**: snake_case (VD: "product_name", "customer_email")
- **Attribute Label**: Tiếng Việt có dấu (VD: "Tên sản phẩm")

### 2. Validation Rules
- **Required**: Chỉ đánh dấu khi thực sự cần thiết
- **Min/Max**: Đặt giới hạn hợp lý
- **Pattern**: Sử dụng regex đơn giản
- **File Size**: 1-5MB cho images, 5-10MB cho documents

### 3. Select Options
- **Label**: Tiếng Việt có dấu, dễ hiểu
- **Value**: Tiếng Anh, snake_case, consistent
- **Order**: Sắp xếp theo thứ tự logic

### 4. File Upload
- **Extensions**: Chỉ cho phép extensions cần thiết
- **Count**: 1-5 files cho images, 1-3 files cho documents
- **Size**: 1-5MB per file

## Troubleshooting

### 1. Form không hiển thị
- Kiểm tra Entity Type có Active không
- Kiểm tra Attributes có được assign không
- Kiểm tra JavaScript console errors

### 2. Validation không hoạt động
- Kiểm tra validation rules
- Kiểm tra frontend và backend validation
- Kiểm tra error messages

### 3. File upload lỗi
- Kiểm tra file extensions
- Kiểm tra file size limits
- Kiểm tra permissions

### 4. Hierarchy không hiển thị
- Kiểm tra parent-child relationship
- Kiểm tra Entity Type configuration
- Kiểm tra Hierarchy view

## Advanced Features

### 1. Dynamic Forms
- Forms tự động generate dựa trên Attributes
- Validation real-time
- Conditional fields (có thể thêm sau)

### 2. Search & Filter
- Search theo tên, code, description
- Filter theo Entity Type
- Pagination tự động

### 3. Bulk Operations
- Select multiple entities
- Bulk delete, update (có thể thêm sau)

### 4. Export/Import
- Export to CSV, JSON (có thể thêm sau)
- Import từ external sources (có thể thêm sau)

## API Endpoints

### 1. Entity Types
```
GET /entity-types - List entity types
POST /entity-types - Create entity type
GET /entity-types/{id} - Get entity type
PUT /entity-types/{id} - Update entity type
DELETE /entity-types/{id} - Delete entity type
```

### 2. Attributes
```
GET /attributes - List attributes
POST /attributes - Create attribute
GET /attributes/{id} - Get attribute
PUT /attributes/{id} - Update attribute
DELETE /attributes/{id} - Delete attribute
```

### 3. Entities
```
GET /eav - List entities
POST /eav - Create entity
GET /eav/{id} - Get entity
PUT /eav/{id} - Update entity
DELETE /eav/{id} - Delete entity
```

### 4. Hierarchy
```
GET /hierarchy - List hierarchy
POST /hierarchy - Create child entity
PUT /hierarchy/{id}/move - Move entity
DELETE /hierarchy/{id} - Delete entity
```

## Performance Tips

### 1. Database
- Sử dụng indexes cho các trường thường query
- Pagination cho large datasets
- Eager loading cho relationships

### 2. Frontend
- Lazy loading cho large forms
- Debounced search
- Caching cho static data

### 3. File Upload
- Compress images trước khi upload
- Use CDN cho file serving
- Implement progress bars

## Security Considerations

### 1. File Upload
- Validate file types và sizes
- Scan for malware
- Store files outside web root

### 2. Data Validation
- Server-side validation
- SQL injection prevention
- XSS protection

### 3. Access Control
- Role-based permissions
- API rate limiting
- Audit logging

## Conclusion

Hệ thống EAV cho phép bạn tạo các modules phức tạp mà không cần viết code database. Chỉ cần định nghĩa Entity Types và Attributes, hệ thống sẽ tự động xử lý phần còn lại.

**Lưu ý**: EAV system phù hợp cho các ứng dụng cần flexibility cao. Nếu bạn có requirements cố định, có thể cân nhắc sử dụng traditional database design.
