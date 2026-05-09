#!/bin/sh

# Crear .env si no existe
if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi

# Escribir variables de entorno al .env
printenv | grep -E "^(APP_|DB_|SESSION_|CACHE_|QUEUE_|MAIL_|REDIS_)" >> /var/www/html/.env

# Generar APP_KEY si está vacío
grep -q "APP_KEY=." /var/www/html/.env || php artisan key:generate --force

# Cachear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migraciones
php artisan migrate --force

# Iniciar PHP-FPM
php-fpm -D

# Iniciar Nginx
nginx -g "daemon off;"