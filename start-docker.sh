#!/bin/bash

# Script to start Docker services for EAV Demo

echo "======================================"
echo "Starting EAV Demo Docker Services"
echo "======================================"

cd /home/namvc/Public/Project/eav-demo

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env from env.docker..."
    cp env.docker .env
fi

# Build and start containers
echo "Building and starting containers..."
docker-compose up -d --build

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 5

# Check container status
echo ""
echo "Container status:"
docker-compose ps

echo ""
echo "======================================"
echo "Installation Commands (run these next):"
echo "======================================"
echo ""
echo "1. Install PHP dependencies:"
echo "   docker-compose exec app composer install"
echo ""
echo "2. Generate application key:"
echo "   docker-compose exec app php artisan key:generate"
echo ""
echo "3. Run migrations:"
echo "   docker-compose exec app php artisan migrate"
echo ""
echo "4. Access application:"
echo "   http://localhost:8080"
echo ""
echo "======================================"

