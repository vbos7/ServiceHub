#!/bin/sh
set -e

cd /var/www/html

# Run migrations before anything else
php artisan migrate --force

# Start supervisor (nginx + php-fpm + queue worker)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
