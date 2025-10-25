# API Documentation - EAV System

## Tổng quan

Hệ thống cung cấp RESTful API cho EAV (Entity-Attribute-Value) system với React + Inertia.js.

**Base URL:** `http://localhost:8000`

**Content-Type:** `application/json`

---

## 1. Entity Types API

### 1.1. Lấy danh sách Entity Types

```http
GET /entity-types
```

**Response:**
```json
[
  {
    "entity_type_id": 1,
    "type_name": "Khách hàng",
    "type_code": "customer",
    "description": "Quản lý khách hàng",
    "is_active": true,
    "attributes_count": 6
  }
]
```

### 1.2. Lấy thông tin Entity Type

```http
GET /entity-types/{id}
```

**Response:**
```json
{
  "entity_type_id": 1,
  "type_name": "Khách hàng",
  "type_code": "customer",
  "description": "Quản lý khách hàng",
  "is_active": true
}
```

### 1.3. Lấy Attributes của Entity Type

```http
GET /api/entity-types/{id}/attributes
```

**Response:**
```json
{
  "entity_type": {
    "entity_type_id": 1,
    "type_name": "Khách hàng",
    "type_code": "customer"
  },
  "attributes": [
    {
      "attribute_id": 1,
      "attribute_code": "full_name",
      "attribute_label": "Họ và tên",
      "backend_type": "varchar",
      "frontend_input": "text",
      "is_required": true,
      "is_searchable": true,
      "is_unique": false,
      "sort_order": 1,
      "options": []
    },
    {
      "attribute_id": 2,
      "attribute_code": "gender",
      "attribute_label": "Giới tính",
      "backend_type": "int",
      "frontend_input": "select",
      "is_required": true,
      "is_searchable": false,
      "is_unique": false,
      "sort_order": 2,
      "options": [
        {
          "option_id": 1,
          "option_value": "1",
          "option_label": "Nam",
          "sort_order": 1
        },
        {
          "option_id": 2,
          "option_value": "2",
          "option_label": "Nữ",
          "sort_order": 2
        }
      ]
    }
  ]
}
```

---

## 2. Attributes API

### 2.1. Lấy danh sách Attributes

```http
GET /attributes
```

**Query Parameters:**
- `entity_type_id` (optional): Lọc theo entity type
- `search` (optional): Tìm kiếm theo tên
- `page` (optional): Số trang

**Response:**
```json
{
  "data": [
    {
      "attribute_id": 1,
      "attribute_code": "full_name",
      "attribute_label": "Họ và tên",
      "backend_type": "varchar",
      "frontend_input": "text",
      "is_required": true,
      "entity_type": {
        "type_name": "Khách hàng",
        "type_code": "customer"
      }
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "per_page": 20,
  "total": 10
}
```

### 2.2. Lấy thông tin Attribute

```http
GET /attributes/{id}
```

**Response:**
```json
{
  "attribute_id": 1,
  "attribute_code": "full_name",
  "attribute_label": "Họ và tên",
  "backend_type": "varchar",
  "frontend_input": "text",
  "is_required": true,
  "is_searchable": true,
  "is_unique": false,
  "sort_order": 1,
  "options": []
}
```

---

## 3. Entities API

### 3.1. Lấy danh sách Entities

```http
GET /eav
```

**Query Parameters:**
- `entity_type_id` (optional): Lọc theo entity type
- `search` (optional): Tìm kiếm
- `page` (optional): Số trang

**Response:**
```json
{
  "data": [
    {
      "entity_id": 1,
      "entity_code": "CUST-001",
      "entity_name": "Nguyễn Văn A",
      "entity_type": {
        "type_name": "Khách hàng",
        "type_code": "customer"
      },
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "per_page": 20,
  "total": 5
}
```

### 3.2. Lấy thông tin Entity chi tiết

```http
GET /eav/{id}
```

**Response:**
```json
{
  "entity_id": 1,
  "entity_code": "CUST-001",
  "entity_name": "Nguyễn Văn A",
  "entity_type": {
    "type_name": "Khách hàng",
    "type_code": "customer"
  },
  "is_active": true,
  "attributes": [
    {
      "attribute_id": 1,
      "attribute_code": "full_name",
      "attribute_label": "Họ và tên",
      "backend_type": "varchar",
      "value": "Nguyễn Văn A"
    },
    {
      "attribute_id": 2,
      "attribute_code": "gender",
      "attribute_label": "Giới tính",
      "backend_type": "int",
      "value": "1"
    }
  ],
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### 3.3. Tạo Entity mới

```http
POST /eav
```

**Body:**
```json
{
  "entity_type_id": 1,
  "entity_code": "CUST-002",
  "entity_name": "Nguyễn Văn B",
  "is_active": true,
  "parent_id": null,
  "attributes": {
    "full_name": "Nguyễn Văn B",
    "gender": "1",
    "phone": "0123456789",
    "email": "nguyenvanb@example.com",
    "address": "123 Đường ABC, Quận 1, TP.HCM"
  }
}
```

**Response:** `200 OK`
```json
{
  "message": "Entity created successfully",
  "entity_id": 2
}
```

### 3.4. Cập nhật Entity

```http
PUT /eav/{id}
```

**Body:**
```json
{
  "entity_name": "Nguyễn Văn B (Updated)",
  "attributes": {
    "full_name": "Nguyễn Văn B (Updated)",
    "phone": "0987654321"
  }
}
```

**Response:** `200 OK`

### 3.5. Xóa Entity

```http
DELETE /eav/{id}
```

**Response:** `200 OK`
```json
{
  "message": "Entity deleted successfully"
}
```

### 3.6. Tìm kiếm Entities

```http
GET /eav/api/search?q={keyword}
```

**Query Parameters:**
- `q`: Từ khóa tìm kiếm
- `entity_type_id` (optional): Lọc theo entity type

**Response:**
```json
[
  {
    "entity_id": 1,
    "entity_code": "CUST-001",
    "entity_name": "Nguyễn Văn A",
    "entity_type": {
      "type_name": "Khách hàng"
    }
  }
]
```

### 3.7. Lấy số lượng Entities theo Type

```http
GET /api/entities/count?entity_type_id={id}
```

**Response:**
```json
{
  "count": 15
}
```

---

## 4. Hierarchy API

### 4.1. Lấy cây Hierarchy

```http
GET /hierarchy
```

**Query Parameters:**
- `entity_type_id` (optional): Lọc theo entity type

**Response:**
```json
[
  {
    "entity_id": 1,
    "entity_code": "WH-001",
    "entity_name": "Kho hàng chính",
    "entity_type": {
      "type_name": "Kho hàng",
      "type_code": "warehouse"
    },
    "parent_id": null,
    "children": [
      {
        "entity_id": 2,
        "entity_code": "ZONE-A",
        "entity_name": "Khu A",
        "entity_type": {
          "type_name": "Khu",
          "type_code": "zone"
        },
        "parent_id": 1,
        "children": []
      }
    ]
  }
]
```

### 4.2. Tạo Entity con

```http
POST /hierarchy
```

**Body:**
```json
{
  "entity_type_id": 2,
  "parent_id": 1,
  "entity_code": "ZONE-A",
  "entity_name": "Khu A - Hàng điện tử",
  "is_active": true,
  "attributes": {
    "zone_name": "Khu A",
    "zone_code": "ZONE-A",
    "temperature": 18.5
  }
}
```

**Response:** `200 OK`

### 4.3. Di chuyển Entity

```http
PUT /hierarchy/{id}/move
```

**Body:**
```json
{
  "new_parent_id": 5
}
```

**Response:** `200 OK`

### 4.4. Xóa Entity (Cascade)

```http
DELETE /hierarchy/{id}
```

**Response:** `200 OK`

---

## 5. Data Types

### Attribute Backend Types

| Type | Description | Example |
|------|-------------|---------|
| `varchar` | String | "Hello" |
| `text` | Long text | "Lorem ipsum..." |
| `int` | Integer | 123 |
| `decimal` | Decimal number | 12.34 |
| `datetime` | Date & Time | "2024-01-01 00:00:00" |
| `file` | File upload | "file.jpg" |

### Frontend Input Types

| Type | Description | Usage |
|------|-------------|-------|
| `text` | Text input | String values |
| `textarea` | Multi-line text | Long text |
| `select` | Single select | One option from list |
| `multiselect` | Multiple select | Multiple options |
| `radio` | Radio buttons | Single choice |
| `checkbox` | Checkbox | Multiple choices |
| `file` | File upload | Images, documents |
| `date` | Date picker | Date values |
| `datetime` | DateTime picker | Date & time |
| `number` | Number input | Numeric values |

---

## 6. Error Handling

### Error Response Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "entity_code": [
      "The entity code field is required."
    ],
    "attributes.full_name": [
      "The full name field is required."
    ]
  }
}
```

### Common HTTP Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `201` | Created |
| `422` | Validation Error |
| `404` | Not Found |
| `500` | Server Error |

---

## 7. Examples

### Example 1: Tạo Customer với dynamic attributes

```javascript
// 1. Lấy attributes của Customer entity type
const attributesResponse = await fetch('/api/entity-types/1/attributes');
const { attributes } = await attributesResponse.json();

// 2. Build form data với validation
const formData = {
  entity_type_id: 1,
  entity_code: 'CUST-003',
  entity_name: 'Nguyễn Văn C',
  is_active: true,
  attributes: {
    full_name: 'Nguyễn Văn C',
    gender: '1', // option value
    phone: '0123456789',
    email: 'nguyenvanc@example.com',
    address: '456 Đường XYZ, Quận 2, TP.HCM'
  }
};

// 3. Submit
const response = await fetch('/eav', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify(formData)
});

if (response.ok) {
  const result = await response.json();
  console.log('Entity created:', result.entity_id);
}
```

### Example 2: Render dynamic form từ attributes

```jsx
// Component nhận attributes từ API
function DynamicForm({ entityTypeId, onSubmit }) {
  const [attributes, setAttributes] = useState([]);
  const [formData, setFormData] = useState({});

  useEffect(() => {
    fetch(`/api/entity-types/${entityTypeId}/attributes`)
      .then(res => res.json())
      .then(data => {
        setAttributes(data.attributes);
        // Initialize form data
        const initial = {};
        data.attributes.forEach(attr => {
          initial[attr.attribute_code] = '';
        });
        setFormData(initial);
      });
  }, [entityTypeId]);

  const renderInput = (attribute) => {
    const { attribute_code, attribute_label, frontend_input, is_required, options } = attribute;
    const value = formData[attribute_code] || '';

    switch (frontend_input) {
      case 'text':
        return (
          <input
            key={attribute_code}
            type="text"
            className="w-full px-3 py-2 border rounded"
            placeholder={attribute_label}
            value={value}
            onChange={(e) => setFormData({...formData, [attribute_code]: e.target.value})}
            required={is_required}
          />
        );
      
      case 'select':
        return (
          <select
            key={attribute_code}
            className="w-full px-3 py-2 border rounded"
            value={value}
            onChange={(e) => setFormData({...formData, [attribute_code]: e.target.value})}
            required={is_required}
          >
            <option value="">Chọn {attribute_label}</option>
            {options.map(opt => (
              <option key={opt.option_id} value={opt.option_value}>
                {opt.option_label}
              </option>
            ))}
          </select>
        );

      case 'textarea':
        return (
          <textarea
            key={attribute_code}
            className="w-full px-3 py-2 border rounded"
            placeholder={attribute_label}
            value={value}
            onChange={(e) => setFormData({...formData, [attribute_code]: e.target.value})}
            required={is_required}
          />
        );

      default:
        return null;
    }
  };

  return (
    <form onSubmit={(e) => { e.preventDefault(); onSubmit(formData); }}>
      {attributes.map(attr => (
        <div key={attr.attribute_id} className="mb-4">
          <label className="block mb-1">
            {attr.attribute_label} {attr.is_required && <span className="text-red-500">*</span>}
          </label>
          {renderInput(attr)}
        </div>
      ))}
      <button type="submit" className="px-4 py-2 bg-blue-500 text-white rounded">
        Lưu
      </button>
    </form>
  );
}
```

### Example 3: Lấy và hiển thị Entity với attributes

```javascript
async function getEntityDetails(entityId) {
  const response = await fetch(`/eav/${entityId}`);
  const entity = await response.json();
  
  console.log('Entity:', entity.entity_name);
  console.log('Attributes:');
  
  entity.attributes.forEach(attr => {
    console.log(`${attr.attribute_label}: ${attr.value}`);
  });
}
```

---

## 8. Notes

### Authentication

Hiện tại API chưa có authentication. Trong production, cần thêm:
- Laravel Sanctum
- JWT tokens
- API keys

### CORS

Nếu frontend chạy trên domain khác, cần config CORS trong `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Pagination

Tất cả list endpoints đều hỗ trợ pagination với parameter `page`:

```javascript
const response = await fetch('/eav?page=2');
const data = await response.json();
console.log('Current page:', data.current_page);
console.log('Total pages:', data.last_page);
```

---

## 9. Contact

Nếu có thắc mắc, liên hệ backend team.

**Version:** 1.0.0  
**Last updated:** 2024-01-01
