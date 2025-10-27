# Advanced Quick Create Attribute với Dynamic Forms & Validation

## Tổng quan

Tính năng Quick Create Attribute đã được nâng cấp với các form fields động và validation rules chi tiết. Người dùng có thể tạo attributes với cấu hình đầy đủ ngay trong modal mà không cần chuyển sang trang tạo attribute riêng biệt.

## Các tính năng mới

### 1. Dynamic Form Fields
- **Input Type Selection**: Chọn loại input sẽ hiển thị các fields tương ứng
- **Real-time Updates**: Form fields thay đổi ngay khi chọn input type
- **Context-aware**: Mỗi input type có các options phù hợp

### 2. Comprehensive Validation Rules
- **Text Validation**: Min/max length, regex pattern
- **Number Validation**: Min/max value, decimal places, step
- **File Validation**: Max count, allowed extensions, file size
- **Email Validation**: Max length, verification requirement
- **URL Validation**: Protocol restriction, max length
- **Date Validation**: Min/max date, future date requirement

### 3. Select Options Management
- **Dynamic Options**: Add/remove options cho select/multiselect
- **Label & Value**: Mỗi option có label hiển thị và value lưu trữ
- **Real-time Management**: Thêm/xóa options ngay trong modal

### 4. Enhanced Input Types
- **Text**: Text input với validation
- **Textarea**: Multi-line text với validation
- **Select**: Dropdown với options
- **Multiselect**: Multiple selection với options
- **Yes/No**: Boolean input
- **File**: File upload với restrictions
- **Number**: Numeric input với validation
- **Email**: Email input với validation
- **URL**: URL input với protocol validation
- **Date**: Date picker với range validation
- **DateTime**: DateTime picker với range validation

## Cách sử dụng

### 1. Mở Quick Create Modal
1. Truy cập `/entity-types/create`
2. Click button "Quick Create" (màu xanh)
3. Modal sẽ mở với form cơ bản

### 2. Điền thông tin cơ bản
- **Attribute Label**: Tên hiển thị (VD: "Họ và tên")
- **Attribute Code**: Mã code (VD: "full_name")
- **Input Type**: Chọn loại input từ dropdown
- **Description**: Mô tả attribute (optional)

### 3. Cấu hình Dynamic Fields
Sau khi chọn Input Type, các fields tương ứng sẽ xuất hiện:

#### Text/Textarea
- **Min Length**: Độ dài tối thiểu
- **Max Length**: Độ dài tối đa
- **Pattern**: Regex pattern cho validation

#### Number
- **Min Value**: Giá trị tối thiểu
- **Max Value**: Giá trị tối đa
- **Decimal Places**: Số chữ số thập phân
- **Step**: Bước nhảy

#### File
- **Max File Count**: Số file tối đa
- **Allowed Extensions**: Các extension được phép
- **Max File Size**: Kích thước file tối đa (KB)

#### Select/Multiselect
- **Options**: Danh sách các lựa chọn
- **Add Option**: Thêm option mới
- **Remove Option**: Xóa option

#### Email
- **Max Length**: Độ dài tối đa
- **Email Verification**: Yêu cầu xác thực email

#### URL
- **Protocol**: Giới hạn protocol (HTTP/HTTPS)
- **Max Length**: Độ dài tối đa

#### Date/DateTime
- **Min Date**: Ngày tối thiểu
- **Max Date**: Ngày tối đa
- **Future Date**: Yêu cầu ngày tương lai

### 4. Cấu hình Validation Rules
- **Required**: Bắt buộc nhập
- **Unique**: Giá trị duy nhất
- **Searchable**: Có thể tìm kiếm
- **Filterable**: Có thể lọc

### 5. Additional Options
- **Placeholder Text**: Text gợi ý
- **Default Value**: Giá trị mặc định
- **Sort Order**: Thứ tự sắp xếp

### 6. Submit Form
1. Click "Create & Select"
2. Attribute sẽ được tạo và tự động chọn
3. Modal đóng và quay về table

## JavaScript Functions

### 1. Dynamic Fields Management
```javascript
function updateQuickCreateFields()     // Cập nhật fields dựa trên input type
function addSelectOptionsFields()      // Thêm fields cho select options
function addFileFields()              // Thêm fields cho file settings
function addNumberFields()            // Thêm fields cho number settings
function addTextValidationFields()    // Thêm fields cho text validation
function addEmailFields()             // Thêm fields cho email settings
function addUrlFields()               // Thêm fields cho URL settings
function addDateFields()              // Thêm fields cho date settings
```

### 2. Select Options Management
```javascript
function addSelectOption()            // Thêm option mới
function removeSelectOption(button)   // Xóa option
```

### 3. Validation Rules Collection
```javascript
function getValidationRules(inputType)  // Lấy validation rules theo input type
function getSelectOptions()            // Lấy danh sách select options
```

## Backend Integration

### 1. AttributeController Updates
- **Validation Rules**: Hỗ trợ validation_rules array
- **Options Support**: Hỗ trợ options array cho select/multiselect
- **Backend Types**: Thêm support cho 'date' backend type
- **JSON Response**: Proper JSON response cho AJAX requests

### 2. AttributeService Updates
- **Validation Rules**: JSON encode validation_rules trước khi lưu
- **Options Handling**: Xử lý options cho select/multiselect
- **Transaction Safety**: Proper DB transaction handling

### 3. Database Schema
- **validation_rules**: JSON column để lưu validation rules
- **options**: Relationship với AttributeOption model
- **backend_type**: Support cho 'date' type

## API Request/Response

### 1. Request Format
```json
{
    "attribute_label": "Full Name",
    "attribute_code": "full_name",
    "frontend_input": "text",
    "backend_type": "varchar",
    "help_text": "Enter full name",
    "placeholder": "Enter your full name",
    "default_value": "",
    "sort_order": 0,
    "is_required": true,
    "is_unique": false,
    "is_searchable": true,
    "is_filterable": true,
    "is_active": true,
    "validation_rules": {
        "min_length": 2,
        "max_length": 100,
        "pattern": "^[a-zA-Z\\s]+$"
    },
    "options": [
        {
            "label": "Option 1",
            "value": "option_1"
        }
    ]
}
```

### 2. Response Format
```json
{
    "success": true,
    "message": "Attribute created successfully",
    "attribute": {
        "attribute_id": 123,
        "attribute_label": "Full Name",
        "attribute_code": "full_name",
        "frontend_input": "text",
        "backend_type": "varchar",
        "validation_rules": "{\"min_length\":2,\"max_length\":100,\"pattern\":\"^[a-zA-Z\\\\s]+$\"}",
        "is_required": true,
        "is_unique": false,
        "is_searchable": true,
        "is_filterable": true
    }
}
```

## Validation Rules Examples

### 1. Text Validation
```javascript
{
    "min_length": 2,
    "max_length": 100,
    "pattern": "^[a-zA-Z\\s]+$"
}
```

### 2. Number Validation
```javascript
{
    "min": 0,
    "max": 999999,
    "decimal_places": 2,
    "step": 0.01
}
```

### 3. File Validation
```javascript
{
    "max_file_count": 5,
    "allowed_extensions": "jpg,png,pdf,doc",
    "max_file_size_kb": 5120
}
```

### 4. Email Validation
```javascript
{
    "max_length": 255,
    "email_verification": true
}
```

### 5. URL Validation
```javascript
{
    "protocol": "https",
    "max_length": 500
}
```

### 6. Date Validation
```javascript
{
    "min_date": "2024-01-01",
    "max_date": "2025-12-31",
    "future_date": false
}
```

## Best Practices

### 1. Input Type Selection
- **Text**: Cho tên, mã, mô tả ngắn
- **Textarea**: Cho mô tả dài, nội dung
- **Select**: Cho lựa chọn có sẵn (giới tính, trạng thái)
- **Multiselect**: Cho nhiều lựa chọn (sở thích, tags)
- **Number**: Cho số liệu, giá cả
- **Email**: Cho email addresses
- **URL**: Cho website links
- **Date**: Cho ngày tháng
- **File**: Cho upload files

### 2. Validation Rules
- **Min/Max Length**: Đặt giới hạn hợp lý
- **Pattern**: Sử dụng regex đơn giản, dễ hiểu
- **Required**: Chỉ đánh dấu khi thực sự cần thiết
- **Unique**: Cẩn thận với unique constraints

### 3. Select Options
- **Label**: Tiếng Việt có dấu, dễ hiểu
- **Value**: Tiếng Anh, snake_case, consistent
- **Order**: Sắp xếp theo thứ tự logic

### 4. File Settings
- **Extensions**: Chỉ cho phép extensions cần thiết
- **File Size**: Đặt giới hạn hợp lý
- **Count**: Giới hạn số lượng files

## Troubleshooting

### 1. Dynamic Fields không hiển thị
- Kiểm tra JavaScript console errors
- Kiểm tra function updateQuickCreateFields()
- Kiểm tra input type value

### 2. Validation Rules không lưu
- Kiểm tra JSON encoding
- Kiểm tra database column type
- Kiểm tra AttributeService

### 3. Select Options không lưu
- Kiểm tra options array structure
- Kiểm tra AttributeOption model
- Kiểm tra relationship

### 4. Form Submission lỗi
- Kiểm tra CSRF token
- Kiểm tra validation rules
- Kiểm tra network tab

## Future Enhancements

### 1. Advanced Validation
- **Custom Validators**: Tạo custom validation rules
- **Conditional Validation**: Validation dựa trên điều kiện
- **Cross-field Validation**: Validation giữa các fields

### 2. UI Improvements
- **Drag & Drop**: Kéo thả để sắp xếp options
- **Preview**: Preview form field trước khi tạo
- **Templates**: Attribute templates

### 3. Integration
- **Import/Export**: Import/export validation rules
- **API Integration**: External validation services
- **Real-time Validation**: Client-side validation

### 4. Performance
- **Lazy Loading**: Load validation rules khi cần
- **Caching**: Cache validation rules
- **Optimization**: Optimize form rendering
