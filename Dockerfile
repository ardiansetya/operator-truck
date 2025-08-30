# Multi-stage build untuk Laravel dengan Vite (Production Ready)
# Stage 1: Build assets dengan Vite
FROM node:18-alpine AS asset-builder
WORKDIR /app

# Copy package files
COPY package*.json ./
COPY vite.config.js ./

# Install dependencies
RUN npm ci

# Copy source files untuk build
COPY resources/ resources/
COPY public/ public/

# Build assets untuk PRODUCTION
RUN npm run build

# Debug: Cek apakah build berhasil
RUN ls -la public/build/ || echo "Build failed!"

# Stage 2: PHP Production
FROM php:8.2-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    mysql-client \
    postgresql-dev \
    icu-dev \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create user
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

WORKDIR /var/www/html

# Copy composer files dan install dependencies DULU
COPY composer.json composer.lock ./

# Install dependencies tanpa autoloader dulu (include dev untuk cache commands)
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application code SETELAH composer install
COPY --chown=www:www . .

# Copy built assets dari node builder ke public folder
COPY --from=asset-builder --chown=www:www /app/public/build ./public/build

# PERBAIKAN: Pastikan build assets ada dan accessible
RUN ls -la public/build/ || echo "Build assets not found!"

# PERBAIKAN: Buat semua direktori yang diperlukan dengan struktur yang benar
RUN mkdir -p bootstrap/cache \
    && mkdir -p storage/app/public \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p /var/www/html/storage/logs

# CRITICAL: Create the missing laravel.log file and set proper ownership
RUN touch /var/www/html/storage/logs/laravel.log

# Set proper permissions untuk semua direktori BEFORE changing ownership
RUN chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache \
    && chmod 644 composer.json composer.lock

# Set ownership AFTER permissions
RUN chown -R www:www /var/www/html \
    && chown -R www:www storage \
    && chown -R www:www bootstrap/cache

# Generate optimized autoloader SETELAH semua file dan permission siap
RUN composer dump-autoload --optimize --classmap-authoritative

# Create .env if not exists (for caching commands)
RUN cp .env.example .env 2>/dev/null || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


# Cache Laravel configuration untuk production (with proper error handling)
RUN php artisan config:clear || true \
    && php artisan cache:clear || true \
    && php artisan config:cache || echo "Config cache skipped - check .env file" \
    && php artisan route:cache || echo "Route cache skipped" \
    && php artisan view:cache || echo "View cache skipped"

# PHP Production optimizations
RUN echo 'opcache.enable=1' > /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.memory_consumption=256' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.interned_strings_buffer=16' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.max_accelerated_files=10000' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.revalidate_freq=0' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.save_comments=1' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/opcache.ini

RUN echo 'memory_limit=512M' > /usr/local/etc/php/conf.d/custom.ini \
    && echo 'upload_max_filesize=100M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'post_max_size=100M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'max_execution_time=300' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'expose_php=Off' >> /usr/local/etc/php/conf.d/custom.ini

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

USER www

EXPOSE 9000

CMD ["php-fpm"]
