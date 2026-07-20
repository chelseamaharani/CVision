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

# Copy package-lock.json if exists (generate if not)
RUN test -f package-lock.json || npm install --package-lock-only

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    && composer dump-autoload --optimize

# Install & build frontend assets
RUN NODE_OPTIONS="--max-old-space-size=512" npm ci && \
    NODE_OPTIONS="--max-old-space-size=512" npm run build && \
    rm -rf node_modules

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache || true

# Create startup script
RUN printf '%s\n' \
    '#!/bin/sh' \
    '' \
    '# Start PHP-FPM in background' \
    'echo "Starting PHP-FPM..."' \
    'php-fpm -D' \
    '' \
    '# Wait for PHP-FPM to be ready (check port 9000)' \
    'echo "Waiting for PHP-FPM to listen on port 9000..."' \
    'for i in $(seq 1 10); do' \
    '    if nc -z 127.0.0.1 9000 2>/dev/null; then' \
    '        echo "PHP-FPM is ready."' \
    '        break' \
    '    fi' \
    '    sleep 1' \
    'done' \
    '' \
    'if ! nc -z 127.0.0.1 9000 2>/dev/null; then' \
    '    echo "WARNING: PHP-FPM may not be ready yet, continuing anyway..."' \
    'fi' \
    '' \
    '# Run Laravel optimizations with runtime env vars' \
    'echo "Running Laravel optimizations..."' \
    'php artisan config:cache 2>/dev/null || true' \
    'php artisan route:cache 2>/dev/null || true' \
    'php artisan view:cache 2>/dev/null || true' \
    'php artisan storage:link 2>/dev/null || true' \
    '' \
    'echo "Starting Nginx..."' \
    'nginx -g "daemon off;"' \
    > /start.sh && chmod +x /start.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD pgrep nginx > /dev/null && pgrep php-fpm > /dev/null || exit 1

CMD ["/start.sh"]
