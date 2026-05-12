#!/bin/sh

if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi

printenv | grep -E "^(APP_|DB_|SESSION_|CACHE_|QUEUE_|MAIL_|REDIS_)" >> /var/www/html/.env

grep -q "APP_KEY=." /var/www/html/.env || php artisan key:generate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Solo migrar, SIN seed en producción
php artisan migrate --force

# php-fpm en foreground
php-fpm &

# Nginx en foreground (mantiene el contenedor vivo)
nginx -g "daemon off;"
