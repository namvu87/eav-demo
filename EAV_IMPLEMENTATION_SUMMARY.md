# EAV DYNAMIC SYSTEM - TÓM TẮT TRIỂN KHAI

## 📊 TỔNG QUAN HỆ THỐNG

### Kiến trúc 5 Layers

```
Layer 1: ENTITY TYPES       → entity_types (Loại đối tượng: Hospital, Zone...)
Layer 2: ATTRIBUTES         → attributes + attribute_options (Trường động)
Layer 3: ENTITIES           → entities (Dữ liệu thực tế)
Layer 4: EAV VALUES         → 6 bảng entity_values_* (Lưu giá trị)
Layer 5: RELATIONS          → entity_relations (Quan hệ đa chiều)
```

---

## 📚 6 NGHIỆP VỤ CHÍNH

### ✅ Nghiệp vụ 1: QUẢN LÝ ENTITY TYPES
**Mục đích:** Tạo tự do các loại đối tượng (Hospital, Department, Room...)

**Bảng:** `entity_types`

**Tính năng:**
- ✅ CRUD entity types
- ✅ Cấu hình icon, color, code_prefix
- ✅ Config JSON cho mỗi type
- ✅ Validation: type_code unique, format lowercase

**API:**
```
POST   /api/entity-types
GET    /api/entity-types
GET    /api/entity-types/{id}
PUT    /api/entity-types/{id}
DELETE /api/entity-types/{id}
```

---

### ✅ Nghiệp vụ 2: QUẢN LÝ ATTRIBUTES
**Mục đích:** Tạo trường động cho từng entity type

**Bảng:** `attributes`, `attribute_groups`, `attribute_options`, `attribute_options_value`

**Tính năng:**
- ✅ CRUD attributes
- ✅ 6 backend types: varchar, text, int, decimal, datetime, file
- ✅ 8 frontend inputs: text, textarea, select, multiselect, date, datetime, yesno, file
- ✅ Validation rules: required, unique, searchable, filterable
- ✅ Select options với multi-language support
- ✅ Attribute groups (tabs)
- ✅ Shared attributes (entity_type_id = NULL)

**API:**
```
POST   /api/attributes
GET    /api/entity-types/{typeId}/attributes
GET    /api/attributes/shared
PUT    /api/attributes/{id}
DELETE /api/attributes/{id}
POST   /api/attributes/{id}/options
```

**Backend Type → Storage Mapping:**
- `varchar` → `entity_values_varchar`
- `text` → `entity_values_text`
- `int` → `entity_values_int`
- `decimal` → `entity_values_decimal`
- `datetime` → `entity_values_datetime`
- `file` → `entity_values_file`

---

### ✅ Nghiệp vụ 3: QUẢN LÝ ENTITIES
**Mục đích:** Tạo và quản lý dữ liệu thực tế

**Bảng:** `entities` + 6 bảng `entity_values_*`

**Tính năng:**
- ✅ Create entity với dynamic attributes
- ✅ Update entity + attributes (upsert values)
- ✅ Delete entity (cascade xóa values)
- ✅ Hierarchy support: parent_id, path, level
- ✅ Validation: required attributes, unique attributes
- ✅ Metadata JSON tự do
- ✅ Audit trail: created_by, updated_by

**API:**
```
POST   /api/entities
GET    /api/entity-types/{typeId}/entities
GET    /api/entities/{id}
PUT    /api/entities/{id}
DELETE /api/entities/{id}
POST   /api/entities/bulk-create
```

**Luồng tạo entity:**
1. Validate entity_code unique
2. Get attributes của type
3. Validate required & unique attributes
4. Calculate path & level (nếu có parent)
5. BEGIN TRANSACTION
6. INSERT entities
7. INSERT vào các bảng entity_values_* (theo backend_type)
8. COMMIT

---

### ✅ Nghiệp vụ 4: QUẢN LÝ RELATIONS
**Mục đích:** Tạo quan hệ đa chiều giữa entities

**Bảng:** `entity_relations`

**Tính năng:**
- ✅ Tạo relation tự do: manages, uses, supplies, located_in...
- ✅ Không giới hạn relation types (VARCHAR, không ENUM)
- ✅ Relation metadata (JSON)
- ✅ Query outgoing & incoming relations
- ✅ Graph traversal (recursive CTE)
- ✅ Prevent circular reference

**API:**
```
POST   /api/relations
GET    /api/entities/{id}/relations
GET    /api/entities/{id}/relations/{type}
DELETE /api/relations/{id}
POST   /api/relations/bulk
```

**Relation Types Examples:**
- `parent_child`: Phân cấp
- `manages`: Quản lý
- `uses`: Sử dụng
- `supplies`: Cung cấp
- `located_in`: Nằm trong
- `depends_on`: Phụ thuộc

---

### ✅ Nghiệp vụ 5: PHÂN CẤP CÂY (Tree Hierarchy)
**Mục đích:** Quản lý cấu trúc cây với Materialized Path

**Bảng:** `entities` (parent_id, path, level)

**Tính năng:**
- ✅ Xem cây phân cấp (Recursive CTE)
- ✅ Get children trực tiếp
- ✅ Get all descendants (path LIKE)
- ✅ Get ancestors / breadcrumb
- ✅ Di chuyển entity (update path cascade)
- ✅ Prevent circular reference
- ✅ Auto calculate level & path

**API:**
```
GET    /api/entity-types/{typeId}/tree
GET    /api/entities/{id}/children
GET    /api/entities/{id}/descendants
GET    /api/entities/{id}/ancestors
POST   /api/entities/{id}/move
```

**Materialized Path Example:**
```
Hospital (id=1):     path = '/1/'          level = 0
  Department (id=2): path = '/1/2/'       level = 1
    Room (id=3):     path = '/1/2/3/'     level = 2
      Bed (id=4):    path = '/1/2/3/4/'   level = 3
```

**Query Descendants:**
```sql
SELECT * FROM entities 
WHERE path LIKE '/1/2/%'  -- All children of entity id=2
```

**Query Ancestors:**
```sql
SELECT e.* FROM entities target
JOIN entities e ON target.path LIKE CONCAT(e.path, '%')
WHERE target.entity_id = 4
ORDER BY e.level;
```

---

### ✅ Nghiệp vụ 6: TÌM KIẾM VÀ LỌC
**Mục đích:** Search & filter entities theo nhiều tiêu chí

**Bảng:** `entities` + `entity_values_*` + `entity_relations`

**Tính năng:**
- ✅ Full-text search (entity_code, entity_name, description)
- ✅ Filter theo entity type
- ✅ Filter theo giá trị attributes động
- ✅ Filter nhiều conditions (AND/OR logic)
- ✅ Filter theo quan hệ
- ✅ Filter theo cấu trúc cây
- ✅ Pagination & sorting
- ✅ Quick search / autocomplete
- ✅ Fulltext search trong text fields
- ✅ Export kết quả

**API:**
```
GET    /api/entities/search?q=keyword&type_id=1
POST   /api/entities/filter
GET    /api/entities/quick-search?q=keyword
POST   /api/entities/export
```

**Filter Operators:**
- `=`, `!=`, `>`, `<`, `>=`, `<=`
- `LIKE`, `IN`, `BETWEEN`

**Advanced Filter Request:**
```json
{
  "entity_type_id": 1,
  "conditions": [
    {"attribute_code": "capacity_beds", "operator": ">", "value": 1000},
    {"attribute_code": "hospital_type", "operator": "=", "value": 1},
    {"attribute_code": "address", "operator": "LIKE", "value": "%Q5%"}
  ],
  "logic": "AND",
  "sort_by": "entity_name",
  "page": 1,
  "per_page": 20
}
```

**Performance:**
- ✅ Indexes trên value columns
- ✅ FULLTEXT index cho text search
- ✅ Redis cache 15 phút
- ✅ Eager loading attributes
- ✅ Separate count query

---

## 🗂️ CẤU TRÚC DATABASE

### Bảng chính (12 tables)

| Bảng | Layer | Records | Mục đích |
|------|-------|---------|----------|
| `entity_types` | 1 | ~10-100 | Định nghĩa loại đối tượng |
| `attributes` | 2 | ~100-1000 | Định nghĩa trường động |
| `attribute_groups` | 2 | ~20-100 | Nhóm attributes thành tabs |
| `attribute_options` | 2 | ~500-5000 | Options cho select/multiselect |
| `attribute_options_value` | 2 | ~500-5000 | Label của options |
| `entities` | 3 | **Millions** | Dữ liệu thực tế |
| `entity_values_varchar` | 4 | **Millions** | Values text ngắn |
| `entity_values_text` | 4 | **Millions** | Values text dài |
| `entity_values_int` | 4 | **Millions** | Values số nguyên |
| `entity_values_decimal` | 4 | **Millions** | Values số thập phân |
| `entity_values_datetime` | 4 | **Millions** | Values ngày giờ |
| `entity_values_file` | 4 | ~Thousands | File uploads |
| `entity_relations` | 5 | **Millions** | Quan hệ giữa entities |

### Indexes quan trọng

```sql
-- entities
CREATE INDEX idx_entities_type ON entities(entity_type_id, is_active);
CREATE INDEX idx_entities_code ON entities(entity_code);
CREATE INDEX idx_entities_tree ON entities(path(255), level);

-- entity_values_*
CREATE INDEX idx_varchar_entity_attr ON entity_values_varchar(entity_id, attribute_id);
CREATE INDEX idx_int_entity_attr ON entity_values_int(entity_id, attribute_id);
CREATE INDEX idx_int_value ON entity_values_int(value);
CREATE FULLTEXT INDEX ft_text_value ON entity_values_text(value);

-- entity_relations
CREATE INDEX idx_rel_source ON entity_relations(source_entity_id, relation_type);
CREATE INDEX idx_rel_target ON entity_relations(target_entity_id, relation_type);
```

---

## 🎯 MA TRẬN NGHIỆP VỤ - BẢNG

| Nghiệp vụ | entity_types | attributes | entities | values_* | relations |
|-----------|--------------|------------|----------|----------|-----------|
| **1. Tạo Entity Type** | ✅ INSERT | ➖ | ➖ | ➖ | ➖ |
| **2. Tạo Attribute** | 🔍 SELECT | ✅ INSERT | ➖ | ➖ | ➖ |
| **3. Tạo Entity** | 🔍 SELECT | 🔍 SELECT | ✅ INSERT | ✅ INSERT | ➖ |
| **4. Cập nhật Entity** | 🔍 SELECT | 🔍 SELECT | ✏️ UPDATE | ✏️ UPDATE | ➖ |
| **5. Xóa Entity** | ➖ | ➖ | ❌ DELETE | ❌ CASCADE | ❌ CASCADE |
| **6. Tạo Relation** | 🔍 SELECT | ➖ | 🔍 SELECT | ➖ | ✅ INSERT |
| **7. Xem Entity Tree** | 🔍 SELECT | ➖ | 🔍 SELECT | ➖ | ➖ |
| **8. Tìm kiếm** | 🔍 SELECT | 🔍 SELECT | 🔍 SELECT | 🔍 SELECT | ➖ |
| **9. Filter theo Attr** | 🔍 SELECT | 🔍 SELECT | 🔍 SELECT | 🔍 SELECT | ➖ |
| **10. Di chuyển Entity** | ➖ | ➖ | ✏️ UPDATE | ➖ | ➖ |

---

## 🚀 IMPLEMENTATION ROADMAP

### Phase 1: Core Foundation ✅
- [x] Database schema (eav2.sql)
- [x] Business analysis (eav5.md)
- [x] Laravel Models cơ bản

### Phase 2: Backend Services (NEXT)
- [ ] EavService - Core logic
- [ ] EntityTypeService
- [ ] AttributeService
- [ ] EntityService (với dynamic attributes)
- [ ] RelationService
- [ ] TreeService
- [ ] SearchService

### Phase 3: Filament Admin
- [ ] EntityTypeResource (CRUD types)
- [ ] AttributeResource (CRUD attributes + options)
- [ ] Dynamic EntityResource (theo từng type)
- [ ] RelationManager
- [ ] Tree view widget
- [ ] Advanced search form

### Phase 4: API & Integration
- [ ] RESTful API controllers
- [ ] API Resources & Transformers
- [ ] Validation Rules
- [ ] API Documentation (Swagger)

### Phase 5: Optimization
- [ ] Redis caching
- [ ] Query optimization
- [ ] Eager loading
- [ ] Database indexing review
- [ ] Performance monitoring

---

## 💡 ĐẶC ĐIỂM NỔI BẬT

### ✅ Ưu điểm

1. **100% Dynamic**
   - Tạo type mới chỉ bằng INSERT
   - Không cần sửa code khi thêm type/attribute

2. **Multi-domain**
   - Factory: Plant → Zone → Workshop → Line
   - Hospital: Hospital → Department → Room → Bed
   - E-commerce: Category → Product → SKU → Variant
   - Cùng 1 database schema!

3. **Flexible Relations**
   - Không giới hạn parent-child
   - Tự do định nghĩa relation types
   - Graph traversal

4. **Scalable**
   - Millions of entities
   - Materialized Path cho tree (O(1) read)
   - Indexed values tables

### ⚠️ Lưu ý

1. **Query Performance**
   - JOIN nhiều bảng values → cần indexes tốt
   - Cache kết quả search
   - Eager loading attributes

2. **Validation Complexity**
   - Required, unique validation ở app layer
   - Type-specific business rules

3. **Learning Curve**
   - Dev cần hiểu EAV pattern
   - Query phức tạp hơn traditional tables

---

## 📖 TÀI LIỆU THAM KHẢO

- **eav2.sql**: Database schema đầy đủ
- **eav5.md**: Business analysis chi tiết 6 nghiệp vụ
- **README.md**: Project overview
- **Models/**: Laravel Eloquent models
- **Services/**: Business logic services (sẽ implement)
- **Resources/**: Filament admin resources

---

## 🔧 TECH STACK

- **Framework**: Laravel 11
- **Admin**: Filament 3
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Search**: MySQL Fulltext + Laravel Scout (optional)
- **API**: RESTful JSON API

---

## 📞 NEXT STEPS

Bạn muốn tôi implement phần nào tiếp theo?

1. **Services Layer** - EntityService, SearchService...
2. **Filament Resources** - Dynamic form builder
3. **API Controllers** - RESTful endpoints
4. **Query Optimization** - Indexes, caching
5. **Testing** - Unit tests, Feature tests

---

**Version:** 2.0  
**Updated:** 2025-10-24  
**Author:** AI Assistant + Business Analyst
