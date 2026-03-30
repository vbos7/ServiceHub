#!/bin/sh
set -e

cd /var/www/html

# Run migrations and seed demo data on first start
php artisan migrate --force
php artisan db:seed --force

# Start supervisor (nginx + php-fpm + queue worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
