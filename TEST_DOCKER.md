# Test Docker Services - EAV Demo

## Bước 1: Start Docker Services

Chạy lệnh sau trong terminal:

```bash
cd /home/namvc/Public/Project/eav-demo
./start-docker.sh
```

Hoặc chạy thủ công:

```bash
cd /home/namvc/Public/Project/eav-demo
docker-compose up -d --build
```

## Bước 2: Kiểm tra containers đang chạy

```bash
docker-compose ps
```

Hoặc:

```bash
docker ps
```

Bạn sẽ thấy 5 containers:
- eav-demo-app (PHP application)
- eav-demo-nginx (Web server)
- eav-demo-mysql (Database)
- eav-demo-redis (Cache)
- eav-demo-node (Node.js)

## Bước 3: Kiểm tra logs

```bash
# Xem logs của tất cả services
docker-compose logs

# Xem logs của một service cụ thể
docker-compose logs app
docker-compose logs nginx
docker-compose logs mysql
```

## Bước 4: Kiểm tra từng service

### Test MySQL

```bash
# Kết nối MySQL
docker-compose exec mysql mysql -u eav_user -ppassword eav_demo

# Trong MySQL shell:
SHOW DATABASES;
SHOW TABLES;
exit;
```

### Test Redis

```bash
docker-compose exec redis redis-cli ping
```

Nếu thấy "PONG" là OK!

### Test Nginx

```bash
curl http://localhost:8080
```

Hoặc mở browser: http://localhost:8080

### Test PHP App

```bash
docker-compose exec app php -v
docker-compose exec app php artisan --version
```

## Bước 5: Setup Application

### Install PHP dependencies:

```bash
docker-compose exec app composer install
```

### Generate application key:

```bash
docker-compose exec app php artisan key:generate
```

### Run migrations:

```bash
docker-compose exec app php artisan migrate
```

Hoặc với seeder:

```bash
docker-compose exec app php artisan migrate --seed
```

## Bước 6: Test Application

Mở browser và truy cập: http://localhost:8080

## Các lệnh hữu ích khác

### Xem logs realtime:
```bash
docker-compose logs -f app
```

### Restart một service:
```bash
docker-compose restart app
```

### Stop tất cả services:
```bash
docker-compose down
```

### Stop và xóa volumes:
```bash
docker-compose down -v
```

### Rebuild một service:
```bash
docker-compose up -d --build app
```

### Access vào container:
```bash
docker-compose exec app bash
docker-compose exec mysql bash
```

## Troubleshooting

### Nếu container không start:

```bash
docker-compose logs app
docker-compose logs mysql
```

### Kiểm tra network:

```bash
docker network ls
docker network inspect eav-demo_eav-network
```

### Kiểm tra volumes:

```bash
docker volume ls
docker volume inspect eav-demo_mysql-data
```

### Rebuild từ đầu:

```bash
docker-compose down -v
docker-compose up -d --build
```

## Test tất cả services một lúc

```bash
# Test script
./test-services.sh
```

