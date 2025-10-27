# Kết nối DBeaver tới MySQL Docker

## Thông tin kết nối

Sau khi start Docker containers, sử dụng thông tin sau để kết nối DBeaver:

```
Host: localhost
Port: 3307
Database: eav_demo
Username: eav_user
Password: password
```

### Hoặc dùng root user:

```
Host: localhost
Port: 3307
Database: eav_demo
Username: root
Password: image.png
```

## Hướng dẫn kết nối DBeaver

### Bước 1: Mở DBeaver

Khởi động DBeaver trên máy của bạn.

### Bước 2: Tạo kết nối mới

1. Click vào **"New Database Connection"** (icon database với dấu +)
   - Hoặc: File → New → Database Connection
   - Hoặc: Nhấn `Ctrl+Shift+N`

2. Chọn **MySQL** từ danh sách databases

3. Nhấn **Next**

### Bước 3: Nhập thông tin kết nối

Trong tab **Main**:

```
Server Host: localhost
Port: 3307
Database: eav_demo
Username: eav_user
Password: password
```

### Bước 3a: Fix lỗi "Public Key Retrieval is not allowed"

Click tab **Driver properties** và thêm:

```
Key: allowPublicKeyRetrieval
Value: true
```

Hoặc thêm vào **Connection URL**:
```
jdbc:mysql://localhost:3307/eav_demo?allowPublicKeyRetrieval=true&useSSL=false
```

### Bước 4: Test kết nối

1. Click nút **"Test Connection"**
2. Nếu lần đầu, DBeaver sẽ download driver MySQL
3. Nhấn **Test Connection**, chọn **Download** nếu có
4. Đợi download và test thành công

### Bước 5: Lưu kết nối

1. Nhập tên cho connection: `EAV Demo (Docker)`
2. Click **Finish**

## Test kết nối bằng command line

```bash
docker-compose exec mysql mysql -u eav_user -ppassword eav_demo -e "SHOW TABLES;"
```

## Thông tin chi tiết từ docker-compose.yml

```yaml
mysql:
  image: mysql:8.0
  ports:
    - "3307:3306"    # Host port 3307 -> Container port 3306
  environment:
    MYSQL_DATABASE: eav_demo
    MYSQL_USER: eav_user
    MYSQL_PASSWORD: password
    MYSQL_ROOT_PASSWORD: rootpassword
```

## Troubleshooting

### 1. Lỗi "Public Key Retrieval is not allowed"

**Nguyên nhân:** MySQL 8.0 yêu cầu retrieve public key lần đầu kết nối.

**Giải pháp 1: Thêm Driver Property**

1. Trong dialog kết nối, click tab **"Driver properties"**
2. Click **"Add"** hoặc tìm trong danh sách
3. Thêm property:
   - Key: `allowPublicKeyRetrieval`
   - Value: `true`
4. Click **Test Connection**

**Giải pháp 2: Sửa Connection URL**

1. Trong tab **Main**, sửa **URL** thành:
   ```
   jdbc:mysql://localhost:3307/eav_demo?allowPublicKeyRetrieval=true&useSSL=false
   ```

**Giải pháp 3: Thay đổi authentication plugin MySQL**

Chạy lệnh sau trong MySQL container:
```bash
docker-compose exec mysql mysql -u root -prootpassword -e "
ALTER USER 'eav_user'@'%' IDENTIFIED WITH mysql_native_password BY 'password';
FLUSH PRIVILEGES;
"
```

**Giải pháp 4: Thêm vào Advanced Settings**

1. Click tab **"Advanced"**
2. Thêm vào **Connection URL**:
   ```
   ?allowPublicKeyRetrieval=true&useSSL=false
   ```

### 2. Connection refused

**Kiểm tra container đang chạy:**
```bash
docker-compose ps
```

**Nếu container không chạy:**
```bash
docker-compose up -d
```

### 2. Can't connect to MySQL

**Kiểm tra MySQL logs:**
```bash
docker-compose logs mysql
```

**Test MySQL từ container:**
```bash
docker-compose exec mysql mysql -u root -prootpassword -e "SELECT 1;"
```

### 3. Access denied

Kiểm tra lại username/password. Có thể reset:
```bash
docker-compose down -v
docker-compose up -d
```

### 4. Port already in use

Nếu port 3307 đã được sử dụng, sửa trong `docker-compose.yml`:
```yaml
ports:
  - "3308:3306"  # Thay đổi port từ 3307 thành 3308
```

## Sử dụng SSH Tunnel (không cần thiết cho local)

Nếu kết nối từ xa, có thể cần SSH tunnel. Với local không cần.

## Queries hữu ích

Sau khi kết nối, thử các queries:

```sql
-- Xem tất cả databases
SHOW DATABASES;

-- Xem tables trong database eav_demo
USE eav_demo;
SHOW TABLES;

-- Xem cấu trúc bảng entities
DESCRIBE entities;

-- Xem dữ liệu
SELECT * FROM entities LIMIT 10;
SELECT * FROM entity_types;
SELECT * FROM attributes;
```

## Import SQL file (nếu có)

Nếu có file `eav2.sql`, có thể import:

```bash
docker-compose exec -T mysql mysql -u eav_user -ppassword eav_demo < eav2.sql
```

Hoặc dùng DBeaver:
1. Right-click database `eav_demo`
2. Chọn **SQL Editor → Execute SQL Script**
3. Chọn file `eav2.sql`
4. Execute

## Backup database

```bash
docker-compose exec mysql mysqldump -u root -prootpassword eav_demo > backup.sql
```

## Thay đổi mật khẩu (nếu cần)

Sửa trong file `.env` hoặc `docker-compose.yml`:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: your_new_password
  MYSQL_PASSWORD: your_new_password
```

Sau đó:
```bash
docker-compose down
docker-compose up -d
```

