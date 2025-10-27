#!/bin/bash

# Check and Test Docker Services

echo "======================================"
echo "EAV Demo - Docker Services Check & Test"
echo "======================================"

cd /home/namvc/Public/Project/eav-demo

# Function to check if command exists
check_command() {
    if command -v $1 &> /dev/null; then
        echo "✓ $1 installed"
        return 0
    else
        echo "✗ $1 not found"
        return 1
    fi
}

echo ""
echo "1. Checking Docker Installation..."
check_command docker
check_command docker-compose

echo ""
echo "2. Checking Docker Service Status..."
if systemctl is-active --quiet docker; then
    echo "✓ Docker service is running"
else
    echo "✗ Docker service is not running"
    echo "  Start with: sudo systemctl start docker"
fi

echo ""
echo "3. Checking Docker Socket Permissions..."
if [ -e /var/run/docker.sock ]; then
    PERMS=$(ls -l /var/run/docker.sock)
    echo "Socket: $PERMS"
    if echo $PERMS | grep -q "rwxrwxrwx\|rw-rw-rw-"; then
        echo "✓ Socket has full permissions"
    else
        echo "⚠ Socket may need permission fix"
        echo "  Run: sudo chmod 666 /var/run/docker.sock"
    fi
else
    echo "✗ Docker socket not found"
fi

echo ""
echo "4. Checking User Docker Group..."
if groups $USER | grep -q docker; then
    echo "✓ User $USER is in docker group"
else
    echo "✗ User $USER is NOT in docker group"
    echo "  Add with: sudo usermod -aG docker $USER"
fi

echo ""
echo "5. Testing Docker Command (without sudo)..."
if docker ps &> /dev/null; then
    echo "✓ Docker works without sudo"
    echo ""
    echo "6. Checking Running Containers..."
    docker ps
    echo ""
    echo "7. Checking Docker Compose Services..."
    docker-compose ps
else
    echo "✗ Docker requires sudo or permission fix"
    echo ""
    echo "Quick Fix Options:"
    echo "  Option 1: Run with sudo"
    echo "    sudo docker ps"
    echo ""
    echo "  Option 2: Fix permissions"
    echo "    sudo chmod 666 /var/run/docker.sock"
    echo ""
    echo "  Option 3: Log out and log back in"
    echo "    (to activate docker group)"
    echo ""
    echo "Testing with sudo:"
    sudo docker ps 2>/dev/null || echo "Sudo password required"
fi

echo ""
echo "======================================"
echo "Summary"
echo "======================================"
echo ""
echo "If Docker doesn't work without sudo:"
echo "1. Fix permissions: sudo chmod 666 /var/run/docker.sock"
echo "2. Or log out/in to activate docker group"
echo ""
echo "To start EAV Demo services:"
echo "  cd /home/namvc/Public/Project/eav-demo"
echo "  docker-compose up -d"
echo ""
echo "======================================"

