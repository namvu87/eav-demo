# Multi-File Upload Feature

## Tổng quan

Tính năng Multi-File Upload cho phép người dùng upload nhiều file cùng lúc với giao diện drag & drop thân thiện, tương tự như trong ảnh bạn gửi. Tính năng này được tích hợp vào cả Quick Create Attribute và EAV Create form.

## Các tính năng chính

### 1. Drag & Drop Interface
- **Drag & Drop**: Kéo thả file trực tiếp vào vùng upload
- **Click to Select**: Click để mở file browser
- **Visual Feedback**: Highlight khi drag over
- **Multiple Files**: Upload nhiều file cùng lúc

### 2. File Validation
- **Extension Check**: Kiểm tra loại file được phép
- **Size Validation**: Kiểm tra kích thước file
- **Count Limit**: Giới hạn số lượng file
- **Real-time Validation**: Validation ngay khi chọn file

### 3. File Preview & Management
- **File List**: Hiển thị danh sách files đã chọn
- **File Info**: Tên file, kích thước, loại file
- **Remove Individual**: Xóa từng file riêng lẻ
- **Clear All**: Xóa tất cả files

### 4. Configuration Options
- **Max File Count**: Số lượng file tối đa (1-20)
- **Allowed Extensions**: Các loại file được phép
- **Max File Size**: Kích thước tối đa mỗi file
- **Size Unit**: KB, MB, GB

## Cách sử dụng

### 1. Trong Quick Create Attribute

#### Tạo File Attribute
1. Mở Quick Create Modal
2. Chọn "File" từ Input Type dropdown
3. Cấu hình file settings:
   - **Số lượng file**: Chọn số file tối đa (VD: 3)
   - **Loại file**: Check các extensions (JPG, PNG, PDF, etc.)
   - **Kích thước**: Đặt kích thước tối đa (VD: 1 MB)
4. Click "Create & Select"

#### File Settings Configuration
- **Số lượng file tải lên**: Input number với min=1, max=20
- **Loại file được phép**: Checkboxes cho các extensions phổ biến
- **Custom Extensions**: Input text cho extensions khác
- **Kích thước file tối đa**: Number input với unit selector

#### Real-time Preview
- **Số file**: Hiển thị số lượng file tối đa
- **Loại file**: Hiển thị extensions được chọn
- **Kích thước**: Hiển thị kích thước tối đa

### 2. Trong EAV Create Form

#### Sử dụng File Attribute
1. Chọn Entity Type có file attributes
2. File upload component sẽ xuất hiện tự động
3. Drag & drop hoặc click để chọn files
4. Xem preview và quản lý files
5. Submit form

#### File Upload Interface
- **Upload Area**: Vùng drag & drop với icon và text
- **File Info**: Hiển thị giới hạn và loại file
- **File List**: Danh sách files đã chọn
- **Actions**: Remove individual files hoặc clear all

## Giao diện Components

### 1. Upload Area
```html
<div class="file-upload-area">
    <div class="file-upload-content">
        <svg class="file-upload-icon">...</svg>
        <div class="file-upload-text">
            <p class="file-upload-title">Kéo thả file vào đây hoặc click để chọn</p>
            <p class="file-upload-subtitle">Tối đa 3 files, kích thước 1 MB mỗi file</p>
            <p class="file-upload-extensions">Loại file: JPG, PNG</p>
        </div>
    </div>
</div>
```

### 2. File Preview List
```html
<div class="file-preview-list">
    <div class="file-preview-header">
        <h4>Files đã chọn</h4>
        <button class="clear-all-btn">Xóa tất cả</button>
    </div>
    <div class="file-list">
        <!-- File items -->
    </div>
</div>
```

### 3. File Item
```html
<div class="file-item">
    <div class="file-info">
        <svg class="file-icon">...</svg>
        <div class="file-details">
            <div class="file-name">filename.jpg</div>
            <div class="file-size">1.2 MB</div>
        </div>
    </div>
    <div class="file-actions">
        <button class="remove-file-btn">X</button>
    </div>
</div>
```

## JavaScript Functions

### 1. Quick Create Functions
```javascript
function updateFilePreview()           // Cập nhật preview real-time
function getFileExtensions()           // Lấy danh sách extensions
function getFileSizeInKB()             // Convert size sang KB
function getValidationRules(inputType) // Lấy validation rules
```

### 2. EAV Create Functions
```javascript
function renderFileUploadComponent(attribute)     // Render file upload component
function initializeFileUploadForAttribute()      // Initialize cho attribute cụ thể
function handleFiles(fileList)                   // Xử lý files được chọn
function validateFile(file)                      // Validate file
function updateFileList()                        // Cập nhật file list
function formatFileSize(bytes)                   // Format file size
```

### 3. Event Handlers
```javascript
// Click to select
uploadArea.addEventListener('click', () => fileInput.click());

// Drag and drop
uploadArea.addEventListener('dragover', handleDragOver);
uploadArea.addEventListener('drop', handleDrop);

// File input change
fileInput.addEventListener('change', handleFileChange);

// Clear all files
clearAllBtn.addEventListener('click', clearAllFiles);
```

## Configuration Examples

### 1. Image Upload (3 files, 2MB each)
```javascript
{
    max_file_count: 3,
    max_file_size_kb: 2048,
    allowed_extensions: 'jpg,png,gif'
}
```

### 2. Document Upload (5 files, 5MB each)
```javascript
{
    max_file_count: 5,
    max_file_size_kb: 5120,
    allowed_extensions: 'pdf,doc,docx,xls,xlsx'
}
```

### 3. Mixed Files (10 files, 1MB each)
```javascript
{
    max_file_count: 10,
    max_file_size_kb: 1024,
    allowed_extensions: 'jpg,png,pdf,doc,txt,zip'
}
```

## Validation Rules

### 1. File Extension Validation
```javascript
function validateFile(file) {
    const extension = file.name.split('.').pop().toLowerCase();
    if (!allowedExtensions.includes(extension)) {
        alert(`File ${file.name} có extension không được phép`);
        return false;
    }
    return true;
}
```

### 2. File Size Validation
```javascript
function validateFile(file) {
    const fileSizeKB = file.size / 1024;
    if (fileSizeKB > maxSize) {
        alert(`File ${file.name} quá lớn`);
        return false;
    }
    return true;
}
```

### 3. File Count Validation
```javascript
function handleFiles(fileList) {
    if (files.length + newFiles.length > maxFiles) {
        alert(`Chỉ được upload tối đa ${maxFiles} files`);
        return;
    }
}
```

## Backend Integration

### 1. Attribute Configuration
```php
// Validation rules stored as JSON
{
    "max_file_count": 3,
    "allowed_extensions": "jpg,png,pdf",
    "max_file_size_kb": 1024
}
```

### 2. Form Submission
```javascript
// File data for form submission
{
    files: [
        {
            name: "file1.jpg",
            size: 1024000,
            type: "image/jpeg",
            lastModified: 1640995200000
        }
    ],
    count: 1,
    config: {
        max_file_count: 3,
        allowed_extensions: "jpg,png,pdf",
        max_file_size_kb: 1024
    }
}
```

## CSS Classes

### 1. Upload Area
- `.file-upload-container`: Container chính
- `.file-upload-area`: Vùng drag & drop
- `.file-upload-area.dragover`: Khi drag over
- `.file-upload-content`: Nội dung upload area
- `.file-upload-icon`: Icon upload
- `.file-upload-text`: Text trong upload area

### 2. Preview List
- `.file-preview-list`: Container preview list
- `.file-preview-header`: Header của preview list
- `.file-preview-title`: Title "Files đã chọn"
- `.clear-all-btn`: Button xóa tất cả
- `.file-list`: Danh sách files
- `.file-item`: Item của từng file

### 3. File Item
- `.file-info`: Thông tin file
- `.file-icon`: Icon file
- `.file-details`: Chi tiết file
- `.file-name`: Tên file
- `.file-size`: Kích thước file
- `.file-actions`: Actions cho file
- `.remove-file-btn`: Button xóa file

## Best Practices

### 1. File Configuration
- **Max Count**: Đặt giới hạn hợp lý (3-10 files)
- **File Size**: 1-5MB cho images, 5-10MB cho documents
- **Extensions**: Chỉ cho phép extensions cần thiết
- **Validation**: Validate cả client và server side

### 2. User Experience
- **Visual Feedback**: Highlight khi drag over
- **Error Messages**: Thông báo lỗi rõ ràng
- **File Preview**: Hiển thị thông tin file
- **Easy Removal**: Dễ dàng xóa files

### 3. Performance
- **File Size Limits**: Giới hạn kích thước hợp lý
- **Count Limits**: Giới hạn số lượng files
- **Validation**: Validate sớm để tránh upload không cần thiết

## Troubleshooting

### 1. Files không upload được
- Kiểm tra file extensions
- Kiểm tra file size limits
- Kiểm tra max file count
- Kiểm tra JavaScript console errors

### 2. Drag & Drop không hoạt động
- Kiểm tra event listeners
- Kiểm tra CSS classes
- Kiểm tra browser compatibility

### 3. File preview không hiển thị
- Kiểm tra DOM elements
- Kiểm tra JavaScript functions
- Kiểm tra CSS styles

## Future Enhancements

### 1. Advanced Features
- **Progress Bar**: Hiển thị tiến trình upload
- **File Compression**: Nén files trước khi upload
- **Thumbnail Preview**: Preview cho images
- **File Sorting**: Sắp xếp files

### 2. Integration
- **Cloud Storage**: Upload lên cloud storage
- **CDN Integration**: Serve files từ CDN
- **Backup**: Backup files tự động
- **Version Control**: Quản lý versions của files

### 3. UI Improvements
- **Grid Layout**: Hiển thị files dạng grid
- **Drag Reorder**: Kéo thả để sắp xếp
- **Bulk Actions**: Actions cho nhiều files
- **Search/Filter**: Tìm kiếm trong files
