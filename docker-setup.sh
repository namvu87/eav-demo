#!/bin/bash

# EAV Demo Docker Setup Script

echo "======================================"
echo "EAV Demo Docker Setup"
echo "======================================"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Please install Docker first."
    echo "Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose is not installed. Please install Docker Compose first."
    echo "Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

# Copy environment file
echo "Copying environment file..."
if [ ! -f .env ]; then
    if [ -f .env.docker ]; then
        cp .env.docker .env
    else
        cp env.docker .env
    fi
    echo "✓ Created .env file"
else
    echo "✓ .env file already exists"
fi

# Start Docker containers
echo "Starting Docker containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Install PHP dependencies
echo "Installing PHP dependencies..."
docker-compose exec -T app composer install --no-interaction

# Install Node dependencies
echo "Installing Node dependencies..."
docker-compose exec -T node npm install

# Generate application key
echo "Generating application key..."
docker-compose exec -T app php artisan key:generate --force

# Run migrations
echo "Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Set permissions
echo "Setting storage permissions..."
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec -T app chmod -R 775 storage bootstrap/cache

echo ""
echo "======================================"
echo "Setup Complete!"
echo "======================================"
echo "Application: http://localhost:8080"
echo "MySQL: localhost:3307"
echo "Redis: localhost:6379"
echo ""
echo "Common commands:"
echo "  docker-compose up -d          # Start containers"
echo "  docker-compose down           # Stop containers"
echo "  docker-compose logs -f app    # View logs"
echo "  docker-compose exec app php artisan migrate  # Run migrations"
echo "======================================"

