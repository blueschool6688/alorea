#!/bin/bash
# =============================================================================
# deploy.sh — Zero-Downtime Blue-Green Deployment
# Chạy trên server production qua SSH từ Jenkins
# Sử dụng: bash deploy.sh <IMAGE_NAME> <IMAGE_TAG>
# =============================================================================

IMAGE_NAME=$1
IMAGE_TAG=$2

FULL_IMAGE="${IMAGE_NAME}:${IMAGE_TAG}"

echo "================================================="
echo "🚀 Bắt đầu Deploy ZERO-DOWNTIME (Blue-Green) Image: ${FULL_IMAGE}"
echo "================================================="

# 1. Kéo image mới
echo "[1/6] Pulling new image on server..."
docker pull ${FULL_IMAGE}

# 2. Xác định môi trường đang chạy (Blue hay Green)
# Luân phiên dùng 2 port: 8080 (Blue) và 8081 (Green)
if docker ps --format '{{.Names}}' | grep -Eq "^perfume-client-blue$"; then
    CURRENT_ENV="blue"
    CURRENT_PORT=8080
    NEW_ENV="green"
    NEW_PORT=8081
elif docker ps --format '{{.Names}}' | grep -Eq "^perfume-client-green$"; then
    CURRENT_ENV="green"
    CURRENT_PORT=8081
    NEW_ENV="blue"
    NEW_PORT=8080
else
    # Lần đầu deploy hoặc container cũ tên khác → chạy green ở port 8081
    CURRENT_ENV="legacy"
    CURRENT_PORT=8080
    NEW_ENV="green"
    NEW_PORT=8081
fi

echo "[2/6] Môi trường hiện tại: ${CURRENT_ENV}. Chuẩn bị bật môi trường mới: ${NEW_ENV} (port ${NEW_PORT})..."

# 3. Khởi động container MỚI
echo "[3/6] Chạy container mới perfume-client-${NEW_ENV}..."
# Xóa container cũ trùng tên (nếu có) để tránh xung đột
docker rm -f perfume-client-${NEW_ENV} 2>/dev/null || true

docker run -d \
    --name perfume-client-${NEW_ENV} \
    --env-file /opt/perfume-client/.env \
    --restart unless-stopped \
    -p ${NEW_PORT}:80 \
    -v perfume_storage:/var/www/html/storage/app \
    -v perfume_logs:/var/www/html/storage/logs \
    ${FULL_IMAGE}

if [ $? -ne 0 ]; then
    echo "❌ LỖI: Không thể khởi chạy container perfume-client-${NEW_ENV}!"
    exit 1
fi

# 4. Đợi container mới sẵn sàng
echo "[4/6] Đợi container khởi động hoàn toàn..."
sleep 5

if ! docker ps --format '{{.Names}}' | grep -Eq "^perfume-client-${NEW_ENV}$"; then
    echo "❌ LỖI: Container perfume-client-${NEW_ENV} không hoạt động hoặc đã crash!"
    docker logs --tail=30 perfume-client-${NEW_ENV}
    exit 1
fi

echo "✅ Container perfume-client-${NEW_ENV} đang chạy!"

# 5. Cập nhật Nginx trỏ sang container mới và reload (KHÔNG DOWNTIME)
echo "[5/6] Cập nhật Nginx → port ${NEW_PORT}..."
# ⚠️ Sửa đường dẫn file Nginx cho đúng với server của bạn
NGINX_CONF="/etc/nginx/sites-available/perfume-client"

if [ -f "$NGINX_CONF" ]; then
    sed -i "s/proxy_pass http:\/\/127.0.0.1:[0-9]*/proxy_pass http:\/\/127.0.0.1:${NEW_PORT}/g" $NGINX_CONF
    systemctl reload nginx
    echo "✅ Nginx đã reload — traffic chuyển sang port ${NEW_PORT}!"
else
    echo "⚠️  CẢNH BÁO: Không tìm thấy file $NGINX_CONF"
    echo "   Hãy sửa biến NGINX_CONF trong docker/deploy.sh cho đúng đường dẫn!"
fi

# 6. Tắt và xóa container CŨ
if [ "$CURRENT_ENV" = "legacy" ]; then
    echo "[6/6] Dọn container cũ (perfume-client)..."
    docker stop perfume-client 2>/dev/null || true
    docker rm   perfume-client 2>/dev/null || true
else
    echo "[6/6] Dọn container cũ (perfume-client-${CURRENT_ENV})..."
    docker stop perfume-client-${CURRENT_ENV} || true
    docker rm   perfume-client-${CURRENT_ENV} || true
fi

# 7. Dọn image cũ
echo "🧹 Dọn image cũ..."
docker image prune -f

# Giữ lại 3 tag gần nhất, xóa tag cũ hơn
docker images "${IMAGE_NAME}" --format "{{.Tag}}" \
    | grep -E '^[0-9]+$' \
    | sort -rn \
    | tail -n +4 \
    | xargs -I{} docker rmi "${IMAGE_NAME}:{}" 2>/dev/null || true

echo "================================================="
echo "🎉 Deploy Zero-Downtime thành công!"
echo "   Image  : ${FULL_IMAGE}"
echo "   Env    : perfume-client-${NEW_ENV} (port ${NEW_PORT})"
echo "================================================="
