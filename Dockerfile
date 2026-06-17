# =============================================================================
# Stage 1: Build React/Vite frontend assets
# =============================================================================
FROM node:22-alpine AS frontend-builder

WORKDIR /app

# Copy package files first for better layer caching
COPY package.json package-lock.json ./

# Install all dependencies (including devDependencies for build)
RUN npm ci --legacy-peer-deps

# Copy only source files needed for Vite build (không copy toàn bộ)
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Build production assets
RUN npm run build

# =============================================================================
# Stage 2: Install PHP/Composer dependencies
# =============================================================================
FROM composer:2.8 AS composer-builder

WORKDIR /app

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

# Copy app source (autoloader cần scan namespace)
COPY app/ ./app/
COPY database/ ./database/
COPY bootstrap/ ./bootstrap/

# Install PHP dependencies (no dev, optimized autoloader)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist \
    --no-scripts \
    --ignore-platform-reqs

# =============================================================================
# Stage 3: Production image
# =============================================================================
FROM php:8.2-fpm-alpine AS production

# Install system dependencies & PHP extensions in one RUN layer
# Gộp apk + docker-php-ext-install + cleanup vào 1 RUN để giảm số layers
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        # Runtime libraries — kept at runtime
        libpng \
        libjpeg-turbo \
        libwebp \
        libzip \
        oniguruma \
        freetype \
        icu-libs \
        gmp \
        # ffmpeg for spatie/laravel-medialibrary
        ffmpeg \
        # Dev headers — only needed for compilation
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        icu-dev \
        gmp-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        bcmath \
        intl \
        gmp \
        pcntl \
        exif \
        opcache \
    && apk del --no-cache \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libzip-dev \
        oniguruma-dev \
        freetype-dev \
        icu-dev \
        gmp-dev \
    && rm -rf /tmp/* /var/cache/apk/*

WORKDIR /var/www/html

# Copy vendor từ composer stage
COPY --from=composer-builder /app/vendor ./vendor

# Copy toàn bộ source code
COPY . .

# BUG FIX: Chỉ copy public/build 1 lần SAU khi copy source,
# tránh bị overwrite bởi COPY . . (lần trước copy 2 lần thừa)
COPY --from=frontend-builder /app/public/build ./public/build

# Tạo thư mục storage framework (Blade compiler cần storage/framework/views)
# Lưu ý: chỉ storage/app được mount volume, các thư mục này phải có sẵn trong image
RUN mkdir -p storage/framework/views \
            storage/framework/sessions \
            storage/framework/cache/data \
            storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ─── Config files ────────────────────────────────────────────────────────────
COPY docker/nginx/default.conf       /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/php.ini              $PHP_INI_DIR/conf.d/app.ini
COPY docker/php/www.conf             /usr/local/etc/php-fpm.d/www.conf

# ─── Entrypoint ─────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
