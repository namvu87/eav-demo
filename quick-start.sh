#!/bin/bash

# Quick Start Script for EAV Demo Docker Setup

cd /home/namvc/Public/Project/eav-demo

echo "======================================"
echo "EAV Demo - Quick Start"
echo "======================================"

# Step 1: Start containers
echo ""
echo "Step 1: Starting Docker containers..."
docker-compose up -d --build

# Wait for services
echo "Waiting for services to be ready..."
sleep 10

# Step 2: Install dependencies
echo ""
echo "Step 2: Installing PHP dependencies..."
docker-compose exec -T app composer install --no-interaction

echo ""
echo "Step 3: Installing Node dependencies..."
docker-compose exec -T node npm install

# Step 4: Generate key
echo ""
echo "Step 4: Generating application key..."
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    docker-compose exec -T app php artisan key:generate --force
fi

# Step 5: Run migrations
echo ""
echo "Step 5: Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Step 6: Set permissions
echo ""
echo "Step 6: Setting storage permissions..."
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec -T app chmod -R 775 storage bootstrap/cache

# Step 7: Test services
echo ""
echo "======================================"
echo "Service Status"
echo "======================================"
docker-compose ps

echo ""
echo "======================================"
echo "Quick Start Complete!"
echo "======================================"
echo ""
echo "Access your application:"
echo "  Web: http://localhost:8080"
echo "  MySQL: localhost:3307"
echo "  Redis: localhost:6379"
echo ""
echo "Common commands:"
echo "  docker-compose logs -f app      # View logs"
echo "  docker-compose ps               # Check status"
echo "  docker-compose restart          # Restart services"
echo "  docker-compose down             # Stop services"
echo ""
echo "======================================"

