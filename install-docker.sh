#!/bin/bash

# Docker and Docker Compose Installation Script for Ubuntu

set -e

echo "======================================"
echo "Docker Installation Script"
echo "======================================"

# Update package index
echo "Updating package index..."
sudo apt-get update

# Install prerequisites
echo "Installing prerequisites..."
sudo apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

# Add Docker's official GPG key
echo "Adding Docker's official GPG key..."
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# Set up Docker repository
echo "Setting up Docker repository..."
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Update package index again
sudo apt-get update

# Install Docker Engine, CLI, and Containerd
echo "Installing Docker..."
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Add current user to docker group
echo "Adding current user to docker group..."
sudo usermod -aG docker $USER

# Enable Docker to start on boot
echo "Enabling Docker to start on boot..."
sudo systemctl enable docker.service
sudo systemctl enable containerd.service

# Start Docker service
echo "Starting Docker service..."
sudo systemctl start docker

# Create symlink for docker-compose
echo "Creating docker-compose symlink..."
sudo ln -sf /usr/libexec/docker/cli-plugins/docker-compose /usr/local/bin/docker-compose

# Verify installation
echo ""
echo "Verifying installation..."
docker --version
docker compose version

echo ""
echo "======================================"
echo "Installation Complete!"
echo "======================================"
echo ""
echo "Important: You need to log out and log back in for group changes to take effect."
echo "Or run: newgrp docker"
echo ""
echo "Test Docker: docker run hello-world"
echo "======================================"

