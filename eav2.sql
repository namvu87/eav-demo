-- ==========================================
-- PURE DYNAMIC EAV SYSTEM
-- Không cần pattern, không cần pre-define structure
-- Chỉ cần INSERT data để tạo mọi thứ!
-- ==========================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS entity_relations;
DROP TABLE IF EXISTS entity_values_datetime;
DROP TABLE IF EXISTS entity_values_decimal;
DROP TABLE IF EXISTS entity_values_int;
DROP TABLE IF EXISTS entity_values_text;
DROP TABLE IF EXISTS entity_values_varchar;
DROP TABLE IF EXISTS entity_values_file;
DROP TABLE IF EXISTS entities;
DROP TABLE IF EXISTS attribute_options_value;
DROP TABLE IF EXISTS attribute_options;
DROP TABLE IF EXISTS attribute_groups;
DROP TABLE IF EXISTS attributes;
DROP TABLE IF EXISTS entity_types;

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- LAYER 1: ENTITY TYPES (Tự do tạo type mới)
-- ==========================================

CREATE TABLE entity_types (
                              entity_type_id INT PRIMARY KEY AUTO_INCREMENT,

    -- Basic info
                              type_code VARCHAR(100) NOT NULL UNIQUE COMMENT 'plant, zone, hospital, department, product...',
                              type_name VARCHAR(255) NOT NULL COMMENT 'Nhà máy, Khu vực, Bệnh viện...',
                              type_name_en VARCHAR(255) COMMENT 'Plant, Zone, Hospital...',

    -- Display config
                              icon VARCHAR(100) COMMENT 'Icon class hoặc emoji',
                              color VARCHAR(20) COMMENT 'Màu hiển thị',
                              code_prefix VARCHAR(10) COMMENT 'PL, ZN, HS, DP...',

    -- Metadata
                              description TEXT,
                              config JSON COMMENT 'Các cấu hình tùy chỉnh',

    -- System
                              is_system BOOLEAN DEFAULT 0 COMMENT 'Type do hệ thống tạo',
                              is_active BOOLEAN DEFAULT 1,
                              sort_order INT DEFAULT 0,

                              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                              updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                              INDEX idx_type_code (type_code),
                              INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Định nghĩa các loại entity - TỰ DO TẠO MỚI';

-- ==========================================
-- LAYER 2: ATTRIBUTES (Fields cho từng entity type)
-- ==========================================

CREATE TABLE attributes (
                            attribute_id INT PRIMARY KEY AUTO_INCREMENT,

    -- Thuộc về entity type nào (hoặc share across types)
                            entity_type_id INT NULL COMMENT 'NULL = shared attribute, INT = specific',

    -- Basic info
                            attribute_code VARCHAR(100) NOT NULL COMMENT 'name, code, area_m2, manager...',
                            attribute_label VARCHAR(255) NOT NULL COMMENT 'Label hiển thị',

    -- Data type & storage
                            backend_type ENUM('varchar', 'text', 'int', 'decimal', 'datetime', 'file') NOT NULL,
                            frontend_input ENUM('text', 'textarea', 'select', 'multiselect', 'date', 'datetime', 'yesno', 'file') NOT NULL,

    -- Validation
                            is_required BOOLEAN DEFAULT 0,
                            is_unique BOOLEAN DEFAULT 0,
                            is_searchable BOOLEAN DEFAULT 1,
                            is_filterable BOOLEAN DEFAULT 0,

                            default_value TEXT,
                            validation_rules JSON COMMENT 'email, url, min, max, regex...',

    -- File upload config (nếu backend_type = file)
                            max_file_count INT DEFAULT 1,
                            allowed_extensions VARCHAR(255) COMMENT 'jpg,png,pdf,dwg',
                            max_file_size_kb INT,

    -- UI config
                            placeholder VARCHAR(255),
                            help_text TEXT,
                            frontend_class VARCHAR(100),
                            sort_order INT DEFAULT 0,

    -- System
                            is_system BOOLEAN DEFAULT 0,
                            is_user_defined BOOLEAN DEFAULT 1,

                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                            UNIQUE KEY uk_type_code (entity_type_id, attribute_code),
                            INDEX idx_backend_type (backend_type),
                            INDEX idx_searchable (is_searchable),

                            FOREIGN KEY fk_attr_entity_type (entity_type_id)
                                REFERENCES entity_types(entity_type_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Định nghĩa attributes - TỰ DO TẠO CHO BẤT KỲ TYPE NÀO';

-- ==========================================
-- LAYER 2.1: ATTRIBUTE GROUPS (Optional - nhóm fields thành tabs)
-- ==========================================

CREATE TABLE attribute_groups (
                                  group_id INT PRIMARY KEY AUTO_INCREMENT,
                                  entity_type_id INT NOT NULL,

                                  group_code VARCHAR(100) NOT NULL COMMENT 'general, technical, advanced',
                                  group_name VARCHAR(255) NOT NULL COMMENT 'Thông tin cơ bản, Kỹ thuật...',

                                  sort_order INT DEFAULT 0,
                                  is_active BOOLEAN DEFAULT 1,

                                  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

                                  UNIQUE KEY uk_type_group (entity_type_id, group_code),
                                  INDEX idx_sort (entity_type_id, sort_order),

                                  FOREIGN KEY fk_group_type (entity_type_id)
                                      REFERENCES entity_types(entity_type_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Nhóm attributes thành tabs (optional)';

-- Link attributes to groups
ALTER TABLE attributes ADD COLUMN group_id INT NULL;
ALTER TABLE attributes ADD FOREIGN KEY fk_attr_group (group_id)
    REFERENCES attribute_groups(group_id) ON DELETE SET NULL;

-- ==========================================
-- LAYER 2.2: ATTRIBUTE OPTIONS (cho select/multiselect)
-- ==========================================

CREATE TABLE attribute_options (
                                   option_id INT PRIMARY KEY AUTO_INCREMENT,
                                   attribute_id INT NOT NULL,

                                   sort_order INT DEFAULT 0,
                                   is_default BOOLEAN DEFAULT 0,

                                   FOREIGN KEY fk_option_attr (attribute_id)
                                       REFERENCES attributes(attribute_id) ON DELETE CASCADE,
                                   INDEX idx_attr_sort (attribute_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attribute_options_value (
                                         value_id INT PRIMARY KEY AUTO_INCREMENT,
                                         option_id INT NOT NULL,

                                         value VARCHAR(255) NOT NULL COMMENT 'Label của option',

                                         FOREIGN KEY fk_value_option (option_id)
                                             REFERENCES attribute_options(option_id) ON DELETE CASCADE,
                                         INDEX idx_option (option_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- LAYER 3: ENTITIES (Data thực tế)
-- ==========================================

CREATE TABLE entities (
                          entity_id INT PRIMARY KEY AUTO_INCREMENT,

    -- Thuộc type gì
                          entity_type_id INT NOT NULL,

    -- Core fields (cố định cho mọi entity)
                          entity_code VARCHAR(100) NOT NULL UNIQUE COMMENT 'PL-001, ZN-COOK-01, HS-001',
                          entity_name VARCHAR(255) NOT NULL,

    -- Hierarchy support (tree structure - optional)
                          parent_id INT NULL COMMENT 'NULL = root entity',
                          path VARCHAR(1000) COMMENT 'Materialized path: /1/5/12/',
                          level INT DEFAULT 0 COMMENT 'Độ sâu: 0=root, 1=level1...',

    -- Metadata
                          description TEXT,
                          metadata JSON COMMENT 'Dữ liệu mở rộng tự do',

    -- Status
                          is_active BOOLEAN DEFAULT 1,
                          sort_order INT DEFAULT 0,

    -- Audit
                          created_by INT,
                          updated_by INT,
                          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                          updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                          INDEX idx_type (entity_type_id),
                          INDEX idx_parent (parent_id),
                          INDEX idx_path (path(255)),
                          INDEX idx_code (entity_code),
                          INDEX idx_active (is_active),

                          FOREIGN KEY fk_entity_type (entity_type_id)
                              REFERENCES entity_types(entity_type_id) ON DELETE CASCADE,
                          FOREIGN KEY fk_entity_parent (parent_id)
                              REFERENCES entities(entity_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Entities - Dữ liệu thực tế của các loại type';

-- ==========================================
-- LAYER 4: ENTITY VALUES (EAV storage)
-- Mỗi backend_type có table riêng
-- ==========================================

-- VARCHAR values
CREATE TABLE entity_values_varchar (
                                       value_id INT PRIMARY KEY AUTO_INCREMENT,
                                       entity_id INT NOT NULL,
                                       attribute_id INT NOT NULL,
                                       value VARCHAR(255),

                                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                       updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                       UNIQUE KEY uk_entity_attr (entity_id, attribute_id),
                                       INDEX idx_attribute (attribute_id),
                                       INDEX idx_value (value(50)),

                                       FOREIGN KEY fk_varchar_entity (entity_id)
                                           REFERENCES entities(entity_id) ON DELETE CASCADE,
                                       FOREIGN KEY fk_varchar_attr (attribute_id)
                                           REFERENCES attributes(attribute_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TEXT values
CREATE TABLE entity_values_text (
                                    value_id INT PRIMARY KEY AUTO_INCREMENT,
                                    entity_id INT NOT NULL,
                                    attribute_id INT NOT NULL,
                                    value TEXT,

                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                    UNIQUE KEY uk_entity_attr (entity_id, attribute_id),
                                    INDEX idx_attribute (attribute_id),
                                    FULLTEXT KEY ft_value (value),

                                    FOREIGN KEY fk_text_entity (entity_id)
                                        REFERENCES entities(entity_id) ON DELETE CASCADE,
                                    FOREIGN KEY fk_text_attr (attribute_id)
                                        REFERENCES attributes(attribute_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INT values
CREATE TABLE entity_values_int (
                                   value_id INT PRIMARY KEY AUTO_INCREMENT,
                                   entity_id INT NOT NULL,
                                   attribute_id INT NOT NULL,
                                   value INT,

                                   created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                   updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                   UNIQUE KEY uk_entity_attr (entity_id, attribute_id),
                                   INDEX idx_attribute (attribute_id),
                                   INDEX idx_value (value),

                                   FOREIGN KEY fk_int_entity (entity_id)
                                       REFERENCES entities(entity_id) ON DELETE CASCADE,
                                   FOREIGN KEY fk_int_attr (attribute_id)
                                       REFERENCES attributes(attribute_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DECIMAL values
CREATE TABLE entity_values_decimal (
                                       value_id INT PRIMARY KEY AUTO_INCREMENT,
                                       entity_id INT NOT NULL,
                                       attribute_id INT NOT NULL,
                                       value DECIMAL(20,4),

                                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                       updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                       UNIQUE KEY uk_entity_attr (entity_id, attribute_id),
                                       INDEX idx_attribute (attribute_id),
                                       INDEX idx_value (value),

                                       FOREIGN KEY fk_decimal_entity (entity_id)
                                           REFERENCES entities(entity_id) ON DELETE CASCADE,
                                       FOREIGN KEY fk_decimal_attr (attribute_id)
                                           REFERENCES attributes(attribute_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DATETIME values
CREATE TABLE entity_values_datetime (
                                        value_id INT PRIMARY KEY AUTO_INCREMENT,
                                        entity_id INT NOT NULL,
                                        attribute_id INT NOT NULL,
                                        value DATETIME,

                                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                        UNIQUE KEY uk_entity_attr (entity_id, attribute_id),
                                        INDEX idx_attribute (attribute_id),
                                        INDEX idx_value (value),

                                        FOREIGN KEY fk_datetime_entity (entity_id)
                                            REFERENCES entities(entity_id) ON DELETE CASCADE,
                                        FOREIGN KEY fk_datetime_attr (attribute_id)
                                            REFERENCES attributes(attribute_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FILE values
CREATE TABLE entity_values_file (
                                    value_id INT PRIMARY KEY AUTO_INCREMENT,
                                    entity_id INT NOT NULL,
                                    attribute_id INT NOT NULL,

                                    file_path VARCHAR(500) NOT NULL,
                                    file_name VARCHAR(255) NOT NULL,
                                    file_size INT NOT NULL COMMENT 'bytes',
                                    mime_type VARCHAR(100) NOT NULL,

                                    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,

                                    INDEX idx_entity_attr (entity_id, attribute_id),
                                    INDEX idx_attribute (attribute_id),

                                    FOREIGN KEY fk_file_entity (entity_id)
                                        REFERENCES entities(entity_id) ON DELETE CASCADE,
                                    FOREIGN KEY fk_file_attr (attribute_id)
                                        REFERENCES attributes(attribute_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- LAYER 5: ENTITY RELATIONS (Đa chiều, đa hướng)
-- ==========================================

CREATE TABLE entity_relations (
                                  relation_id INT PRIMARY KEY AUTO_INCREMENT,

    -- Source và target entities
                                  source_entity_id INT NOT NULL,
                                  target_entity_id INT NOT NULL,

    -- Loại quan hệ (TỰ DO ĐỊNH NGHĨA!)
                                  relation_type VARCHAR(100) NOT NULL COMMENT 'parent_child, uses, supplies, manages, located_in, depends_on...',

    -- Metadata bổ sung
                                  relation_data JSON COMMENT 'Dữ liệu mở rộng cho relation',

    -- Thứ tự và trạng thái
                                  sort_order INT DEFAULT 0,
                                  is_active BOOLEAN DEFAULT 1,

                                  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                  INDEX idx_source (source_entity_id, relation_type),
                                  INDEX idx_target (target_entity_id, relation_type),
                                  INDEX idx_type (relation_type),

                                  FOREIGN KEY fk_rel_source (source_entity_id)
                                      REFERENCES entities(entity_id) ON DELETE CASCADE,
                                  FOREIGN KEY fk_rel_target (target_entity_id)
                                      REFERENCES entities(entity_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Quan hệ đa chiều giữa các entities';


