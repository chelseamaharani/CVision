# ============================================================
# Dockerfile — Laravel 13 + PHP 8.3 + Nginx (Optimized for Railway Free Tier)
# ============================================================

FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions (single thread to save memory)
RUN apk add --no-cache \
    nginx \
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
    && docker-php-ext-install -j1 \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies (production only, no dev)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    && composer dump-autoload --optimize

# Install & build frontend assets (limit Node memory)
RUN NODE_OPTIONS="--max-old-space-size=256" npm install --no-optional --ignore-scripts \
    && NODE_OPTIONS="--max-old-space-size=256" npm run build \
    && rm -rf node_modules

# Laravel optimization
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Create storage link
RUN php artisan storage:link || true

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=15s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]