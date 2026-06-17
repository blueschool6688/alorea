#!/bin/sh
set -e

echo "🚀 Starting Laravel application..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY not set. Generating..."
    php artisan key:generate --force
fi

# Cache configuration for production
echo "📦 Caching config, routes and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations (only if DB is reachable)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "🗃️  Running migrations..."
    php artisan migrate --force --no-interaction
fi

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

echo "✅ Application ready. Starting services..."
exec "$@"
