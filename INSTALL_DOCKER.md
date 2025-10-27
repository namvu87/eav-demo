# Hướng dẫn cài đặt Docker và Docker Compose

## Cách 1: Chạy script tự động (Khuyên dùng)

Mở terminal và chạy lệnh sau:

```bash
cd /home/namvc/Public/Project/eav-demo
bash install-docker.sh
```

Nhập mật khẩu khi được hỏi.

Sau khi cài đặt xong, bạn cần **log out và log in lại** để áp dụng quyền nhóm docker.

## Cách 2: Cài đặt thủ công từng bước

### Bước 1: Cài đặt dependencies

```bash
sudo apt-get update

sudo apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release
```

### Bước 2: Thêm Docker's GPG key

```bash
sudo install -m 0755 -d /etc/apt/keyrings

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

sudo chmod a+r /etc/apt/keyrings/docker.gpg
```

### Bước 3: Thiết lập repository

```bash
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```

### Bước 4: Cài đặt Docker

```bash
sudo apt-get update

sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

### Bước 5: Thêm user vào docker group

```bash
sudo usermod -aG docker $USER
```

### Bước 6: Khởi động Docker

```bash
sudo systemctl enable docker.service
sudo systemctl enable containerd.service
sudo systemctl start docker
```

### Bước 7: Tạo symlink cho docker-compose

```bash
sudo ln -sf /usr/libexec/docker/cli-plugins/docker-compose /usr/local/bin/docker-compose
```

### Bước 8: Kiểm tra cài đặt

```bash
docker --version
docker compose version
```

### Bước 9: Log out và log in lại

**QUAN TRỌNG:** Bạn cần **log out và log in lại** để áp dụng quyền nhóm docker.

Hoặc chạy lệnh tạm thời:
```bash
newgrp docker
```

## Test Docker

Sau khi cài đặt xong, test Docker:

```bash
docker run hello-world
```

Nếu thấy output thành công, Docker đã được cài đặt đúng!

## Troubleshooting

### Lỗi "permission denied"

Nếu gặp lỗi permission, chạy:
```bash
newgrp docker
```

Hoặc log out và log in lại.

### Không tìm thấy docker-compose

Nếu lệnh `docker-compose` không hoạt động, thử:
```bash
docker compose version
```

(Docker Compose v2 sử dụng `docker compose` thay vì `docker-compose`)

### Uninstall Docker (nếu cần)

```bash
sudo apt-get purge docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo rm -rf /var/lib/docker
sudo rm -rf /var/lib/containerd
```

