# Docker Setup for EAV Demo

This project uses Docker and Docker Compose for containerization.

## Prerequisites

- Docker (version 20.10 or later)
- Docker Compose (version 2.0 or later)

## Quick Start

1. **Copy environment file:**
   ```bash
   cp .env.docker .env
   ```

2. **Build and start containers:**
   ```bash
   docker-compose up -d --build
   ```

3. **Install dependencies:**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   ```

4. **Generate application key:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

5. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

6. **Access the application:**
   - Web: http://localhost:8080
   - MySQL: localhost:3307
   - Redis: localhost:6379

## Docker Services

- **app**: PHP 8.2 FPM application
- **nginx**: Web server
- **mysql**: MySQL 8.0 database
- **redis**: Redis cache
- **node**: Node.js for frontend development

## Common Commands

### Start containers
```bash
docker-compose up -d
```

### Stop containers
```bash
docker-compose down
```

### View logs
```bash
docker-compose logs -f app
```

### Execute commands in container
```bash
docker-compose exec app php artisan migrate
docker-compose exec app composer update
docker-compose exec app npm run dev
```

### Rebuild containers
```bash
docker-compose down
docker-compose up -d --build
```

### Access MySQL
```bash
docker-compose exec mysql mysql -u eav_user -ppassword eav_demo
```

### Clear cache
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

## Development

For development with hot reload:
```bash
docker-compose up
```

The `node` service will automatically rebuild frontend assets when changes are detected.

## Database

The database is initialized with the SQL file from `eav2.sql` if it exists. The MySQL service persists data in a Docker volume.

## Troubleshooting

### Permission issues
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Restart services
```bash
docker-compose restart app nginx
```

## Notes

- The application runs on port 8080
- MySQL runs on port 3307 to avoid conflicts
- Redis runs on port 6379
- All containers are on the same network (`eav-network`)

