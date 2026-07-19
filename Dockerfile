# ============================================================
# Dockerfile — Laravel 13 + PHP 8.3 + Nginx
# ============================================================

FROM php:8.3-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    && composer dump-autoload --optimize

# Install & build frontend assets
RUN npm ci --ignore-scripts && npm run build && rm -rf node_modules

# Laravel optimization
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# ============================================================
# Production stage
# ============================================================
FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx curl && \
    docker-php-ext-install pdo pdo_mysql

WORKDIR /app

# Copy from builder
COPY --from=builder /app /app
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Create storage link
RUN php artisan storage:link || true

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]