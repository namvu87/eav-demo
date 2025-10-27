# Fix Docker Permissions

## Problem
Docker commands require sudo even though user is in docker group.

## Solutions

### Solution 1: Log out and log back in (Recommended)
This is the cleanest solution:
```bash
# Just log out of your desktop session and log back in
```

### Solution 2: Start a new shell with docker group active
```bash
# Start bash with docker group active
sg docker -c 'bash'

# Then test
docker ps
```

### Solution 3: Modify docker socket permissions temporarily (Quick fix)
```bash
sudo chmod 666 /var/run/docker.sock
```

Then test: `docker ps`

**Note:** This is not recommended for security reasons but works for development.

### Solution 4: Create an alias
Add to your `~/.bashrc` or `~/.zshrc`:
```bash
alias docker='sg docker -c docker'
alias docker-compose='sg docker -c "docker compose"'
```

Then reload:
```bash
source ~/.bashrc  # or source ~/.zshrc
```

### Solution 5: Use our wrapper script
```bash
cd /home/namvc/Public/Project/eav-demo
./docker-wrapper.sh ps
./docker-wrapper.sh compose up -d
```

## Verify user is in docker group

```bash
groups $USER | grep docker
```

Should show `docker` in the output.

If not, add user to docker group:
```bash
sudo usermod -aG docker $USER
# Then log out and log back in
```

## Check docker socket permissions

```bash
ls -la /var/run/docker.sock
```

Should show: `srw-rw---- 1 root docker`

## Quick Start with Fix

Run these commands in order:

1. **Add to docker group (if needed):**
   ```bash
   sudo usermod -aG docker $USER
   ```

2. **Temporary fix (immediate):**
   ```bash
   sudo chmod 666 /var/run/docker.sock
   ```

3. **Test:**
   ```bash
   docker ps
   docker compose version
   ```

4. **Start your project:**
   ```bash
   cd /home/namvc/Public/Project/eav-demo
   docker compose up -d
   ```

5. **For permanent fix:**
   - Log out and log back in, OR
   - Restart your computer

## Current Status

Run this to check:
```bash
echo "User groups: $(groups $USER)"
echo "Docker socket: $(ls -la /var/run/docker.sock)"
```

