# Hướng dẫn tạo Hierarchy (Quan hệ cha-con) cho Entities

## Tổng quan

Trong hệ thống EAV, bạn có thể tạo các entities có quan hệ **cha-con (parent-child)** để xây dựng cấu trúc phân cấp.

## Ví dụ: Kho hàng → Khu → Vùng → Dãy kệ

### 1. Cấu trúc Hierarchy

```
Kho hàng (Warehouse)
├── Khu A (Zone A)
│   ├── Vùng A1 (Area A1)
│   │   ├── Dãy kệ A1-01 (Shelf A1-01)
│   │   └── Dãy kệ A1-02 (Shelf A1-02)
│   └── Vùng A2 (Area A2)
└── Khu B (Zone B)
    └── Vùng B1 (Area B1)
```

### 2. Các Entity Types đã được tạo

- **Warehouse** (Kho hàng) - Root level
- **Zone** (Khu) - Con của Warehouse
- **Area** (Vùng) - Con của Zone
- **Shelf** (Dãy kệ) - Con của Area

## Cách tạo Hierarchy mới

### Bước 1: Truy cập trang Hierarchy

```
http://localhost:8000/hierarchy
```

Hoặc click vào menu **Hierarchy** trong sidebar.

### Bước 2: Tạo Entity cha (Root)

1. Click vào **"Add Child"** hoặc **"+ Add Entity Type"**
2. Chọn **Entity Type**: "Kho hàng"
3. Nhập thông tin:
   - **Entity Code**: `WH-001`
   - **Entity Name**: `Kho hàng chính`
4. Nhập **Attributes** (nếu có)
5. Click **Save**

Kết quả: Bạn đã tạo **Warehouse** không có parent (root entity).

### Bước 3: Tạo Entity con (Child)

**Cách 1: Từ trang Hierarchy**

1. Trong cây hierarchy, click vào nút **"+"** (PlusIcon) bên cạnh entity cha
2. Chọn **Entity Type**: "Khu"
3. Nhập thông tin:
   - **Entity Code**: `ZONE-A`
   - **Entity Name**: `Khu A - Hàng điện tử`
4. **Parent ID** sẽ tự động được điền
5. Nhập **Attributes**
6. Click **Save**

**Cách 2: Từ trang EAV (Tạo entity mới)**

1. Truy cập `http://localhost:8000/eav/create?parent_id=X&entity_type_id=Y`
2. Nhập thông tin entity
3. **Parent ID** đã được truyền qua URL
4. Click **Save**

### Bước 4: Xem Hierarchy

1. Truy cập `http://localhost:8000/hierarchy`
2. Bạn sẽ thấy cây hierarchy với các node có thể mở rộng/thu gọn
3. Click vào icon **ChevronRight** để mở rộng node
4. Click vào icon **ChevronDown** để thu gọn node

## Quản lý Hierarchy

### Thêm entity con

1. Tìm entity cha trong cây hierarchy
2. Click nút **"+"** bên cạnh entity cha
3. Chọn entity type và nhập thông tin
4. **Parent ID** được tự động điền

### Sửa entity

1. Click icon **Edit** (pencil) bên cạnh entity
2. Sửa thông tin
3. Lưu ý: Không thể đổi parent trực tiếp từ đây

### Di chuyển entity (Move)

1. Click icon **Settings** (gear)
2. Chọn **Move**
3. Chọn parent mới
4. Entity và tất cả con cháu sẽ được di chuyển

### Xóa entity

1. Click icon **Delete** (trash) bên cạnh entity
2. Xác nhận xóa
3. **Lưu ý**: Xóa entity sẽ xóa tất cả entities con

## Data mẫu đã tạo sẵn

Seeder đã tạo sẵn hierarchy mẫu:

```
Kho hàng chính (WH-001)
├── Khu A - Hàng điện tử (ZONE-A)
│   └── Vùng A1 - Điện thoại (AREA-A1)
│       ├── Dãy kệ A1-01 (SHELF-A1-01)
│       └── Dãy kệ A1-02 (SHELF-A1-02)
└── Khu B - Hàng may mặc (ZONE-B)
```

## Ví dụ thực tế

### Case 1: Tạo "Phân khu" trong "Bố cục kho"

**Entity Types cần có:**
- "Bố cục kho" (Warehouse Layout)
- "Phân khu" (Sub Zone)

**Các bước:**

1. **Tạo "Bố cục kho" (Entity cha):**
   - Entity Type: `Bố cục kho`
   - Entity Code: `BCK-001`
   - Entity Name: `Bố cục kho hàng chính`
   - Parent: `NULL` (root)

2. **Tạo "Phân khu" (Entity con):**
   - Entity Type: `Phân khu`
   - Entity Code: `PKH-001`
   - Entity Name: `Phân khu A`
   - Parent: `BCK-001`

3. **Kết quả:**
   ```
   Bố cục kho hàng chính (BCK-001)
   └── Phân khu A (PKH-001)
   ```

### Case 2: Tạo nhiều cấp

**Tạo cấu trúc 3 cấp: Tầng → Phòng → Vị trí**

1. **Tạo "Tầng 1" (Entity cha):**
   - Entity Code: `TANG-001`
   - Entity Name: `Tầng 1`

2. **Tạo "Phòng A" (Entity con):**
   - Entity Code: `PHONG-A`
   - Entity Name: `Phòng A`
   - Parent: `TANG-001`

3. **Tạo "Vị trí 101" (Entity con của Phòng A):**
   - Entity Code: `VT-101`
   - Entity Name: `Vị trí 101`
   - Parent: `PHONG-A`

**Kết quả:**
```
Tầng 1 (TANG-001)
└── Phòng A (PHONG-A)
    └── Vị trí 101 (VT-101)
```

## API Endpoints

### 1. Lấy cây hierarchy

```http
GET /hierarchy
```

**Query Parameters:**
- `entity_type_id`: Lọc theo entity type

**Response:**
```json
[
  {
    "entity_id": 1,
    "entity_name": "Kho hàng chính",
    "entity_code": "WH-001",
    "entity_type": {
      "type_name": "Kho hàng",
      "type_code": "warehouse"
    },
    "children": [
      {
        "entity_id": 2,
        "entity_name": "Khu A",
        "entity_code": "ZONE-A",
        "children": [...]
      }
    ]
  }
]
```

### 2. Tạo entity con

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

### 3. Di chuyển entity

```http
PUT /hierarchy/{id}/move
```

**Body:**
```json
{
  "new_parent_id": 5
}
```

### 4. Xóa entity (cascade)

```http
DELETE /hierarchy/{id}
```

## Best Practices

### 1. Đặt tên Entity Code có ý nghĩa

**Good:**
- `WH-001` (Warehouse-001)
- `ZONE-A` (Zone-A)
- `SHELF-A1-01` (Shelf-Area1-01)

**Bad:**
- `E1`, `E2`, `E3` (không mô tả được)
- `test123`
- `new_entity`

### 2. Giữ cấu trúc phẳng

Tránh hierarchy quá sâu (>5 levels):
```
Bad: A → B → C → D → E → F → G
Good: A → B → C (≤3-4 levels)
```

### 3. Sử dụng Attributes để lưu metadata

- Địa chỉ
- Mô tả
- Thông tin bổ sung

### 4. Sắp xếp bằng `sort_order`

Đặt `sort_order` cho entities để hiển thị theo thứ tự mong muốn.

## Troubleshooting

### Không thấy "Add Child" button

**Nguyên nhân:** Bạn chưa có entity nào

**Giải pháp:** Tạo entity root trước

### Không thể xóa entity

**Nguyên nhân:** Entity có con

**Giải pháp:** Xóa tất cả con trước, hoặc dùng cascade delete

### Entity con không hiển thị

**Nguyên nhân:** Query chưa load `children`

**Giải pháp:** Check controller có `with('children')` không

### Parent ID không được điền

**Nguyên nhân:** URL thiếu `parent_id`

**Giải pháp:** Thêm `?parent_id=X` vào URL

## Kết luận

Với hệ thống hierarchy:
- ✅ Tạo entities có quan hệ cha-con
- ✅ Xem cây hierarchy trực quan
- ✅ Di chuyển entities
- ✅ Quản lý cascade delete
- ✅ Mở rộng không giới hạn levels

Chúc bạn sử dụng tốt! 🎉
