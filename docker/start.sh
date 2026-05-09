#!/bin/sh

# Generar APP_KEY si no existe
php artisan key:generate --force

# Limpiar y cachear config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Correr migraciones
php artisan migrate --force

# Iniciar PHP-FPM en background
php-fpm -D

# Iniciar Nginx
nginx -g "daemon off;"
