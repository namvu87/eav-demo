# Rebuild Docker Containers

Đã fix lỗi thiếu PHP extensions (intl và zip). Các bước rebuild:

## Bước 1: Stop và xóa containers cũ

```bash
cd /home/namvc/Public/Project/eav-demo
docker-compose down
```

## Bước 2: Rebuild containers

```bash
docker-compose up -d --build
```

Hoặc nếu dùng sudo:
```bash
sudo docker-compose down
sudo docker-compose up -d --build
```

## Bước 3: Kiểm tra

```bash
docker-compose ps
docker-compose logs app
```

## Bước 4: Setup application

```bash
# Install dependencies
docker-compose exec app composer install

# Generate key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --seed
```

## Quick Rebuild

```bash
cd /home/namvc/Public/Project/eixa-demo
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Nếu vẫn lỗi

Xóa toàn bộ và rebuild từ đầu:

```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

