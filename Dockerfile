FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    sqlite \
    sqlite-dev \
    curl \
    unzip \
    git \
    supervisor

# PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite pcntl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install ALL composer deps (dev included — needed for artisan during npm build)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Copy full source
COPY . .

# Create build-time .env (key generated with PHP directly, no artisan needed)
RUN cp .env.example .env \
    && sed -i "s|^APP_KEY=.*|APP_KEY=base64:$(php -r 'echo base64_encode(random_bytes(32));')|" .env \
    && touch database/database.sqlite

# Build frontend (wayfinder plugin calls php artisan internally)
RUN npm ci && npm run build && npm prune --production

# Switch to production composer deps + cleanup
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer dump-autoload --optimize

# Laravel directory setup
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database \
    && rm .env

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
