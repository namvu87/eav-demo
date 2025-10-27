#!/bin/bash

# Fix Docker permissions without restarting session

echo "Fixing Docker permissions..."

# Add user to docker group if not already added
sudo usermod -aG docker $USER

# Change Docker socket permissions
sudo chmod 666 /var/run/docker.sock
sudo chown root:docker /var/run/docker.sock

echo "Done! Please log out and log back in for full effect."
echo ""
echo "Or run this command in your current shell:"
echo "  sg docker -c 'bash'"
echo ""
echo "Then test with: docker ps"

