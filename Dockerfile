# ==========================================
# 1. Node Build Stage (Compile Frontend)
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Copy package files
COPY package.json package-lock.json* yarn.lock* pnpm-lock.yaml* ./

# Install Node dependencies
RUN npm install --legacy-peer-deps

# Copy all source files to build the assets
COPY . .

# Run Vite build to generate public/build
RUN npm run build

# ==========================================
# 2. PHP / Nginx Final Stage
# ==========================================
FROM php:8.3.9-fpm-alpine3.20 AS base
WORKDIR /app

# Install Nginx, Supervisor, Composer & PHP extensions
COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

# Install runtime dependencies and Nginx/Supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    freetype \
    libjpeg-turbo \
    libpng \
    libzip \
    # Install build dependencies temporarily
    && apk add --no-cache --virtual .build-deps \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    # Configure and install PHP extensions
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql pcntl gd exif zip \
    # Remove build dependencies to save image space
    && apk del .build-deps

# Copy composer files separately to cache dependencies layer
COPY composer.json composer.lock* ./

# Install dependencies (Optimize for production) & clear composer cache
RUN composer install --no-dev --no-scripts --no-autoloader \
    && rm -rf /root/.composer

# Copy source code and files
COPY . .

# Copy compiled frontend assets from Node stage
COPY --from=frontend-builder /app/public/build ./public/build

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Setup Nginx and Supervisor Configs
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Permissions
RUN mkdir -p storage/logs storage/framework/{sessions,views,cache} \
    && chmod -R 777 storage bootstrap/cache \
    && chmod -R 777 storage \
    && chown -R www-data:www-data /app

# Ensure we expose port 80 to Caprover
EXPOSE 80

# This command runs supervisor to keep Nginx & PHP-FPM alive
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
