#!/bin/sh
set -e

echo "🚀 Starting Laravel application..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY not set. Generating..."
    php artisan key:generate --force
fi

# Đặt lại permission sau khi mkdir (volume mount có thể thay đổi owner)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Cache configuration for production
echo "📦 Caching config and routes..."
php artisan config:cache
php artisan route:cache

# Cache views — chỉ chạy nếu có views, bỏ qua nếu lỗi
echo "🖼  Caching views..."
php artisan view:cache || echo "⚠️  View cache skipped (no views or path not found)"

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

echo "✅ Application ready. Starting services..."
exec "$@"
