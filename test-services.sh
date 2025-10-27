#!/bin/bash

# Test script for Docker services

echo "======================================"
echo "EAV Demo - Docker Services Test"
echo "======================================"

cd /home/namvc/Public/Project/eav-demo

echo ""
echo "1. Checking containers status..."
docker-compose ps

echo ""
echo "2. Testing MySQL..."
docker-compose exec -T mysql mysql -u eav_user -ppassword -e "SELECT VERSION();" 2>/dev/null && echo "✓ MySQL OK" || echo "✗ MySQL Failed"

echo ""
echo "3. Testing Redis..."
docker-compose exec -T redis redis-cli ping 2>/dev/null && echo "✓ Redis OK" || echo "✗ Redis Failed"

echo ""
echo "4. Testing Nginx..."
curl -s http://localhost:8080 > /dev/null && echo "✓ Nginx OK (port 8080 accessible)" || echo "✗ Nginx Failed"

echo ""
echo "5. Testing PHP application..."
docker-compose exec -T app php -v > /dev/null && echo "✓ PHP OK" || echo "✗ PHP Failed"

echo ""
echo "6. Testing Node.js..."
docker-compose exec -T node node --version > /dev/null && echo "✓ Node.js OK" || echo "✗ Node.js Failed"

echo ""
echo "======================================"
echo "Test Summary"
echo "======================================"
echo "Containers: $(docker-compose ps -q | wc -l) running"
echo "MySQL port: 3307"
echo "Redis port: 6379"
echo "Web: http://localhost:8080"
echo "======================================"

