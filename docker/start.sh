#!/bin/sh
set -e

echo "[start] Iniciando contenedor Laravel..."

# ── 1. ENV ──────────────────────────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi

# Vuelca solo las vars relevantes, sin duplicar
printenv | grep -E "^(APP_|DB_|SESSION_|CACHE_|QUEUE_|MAIL_|REDIS_|LIVEWIRE_)" \
    | while IFS='=' read -r key value; do
        grep -q "^${key}=" /var/www/html/.env || echo "${key}=${value}" >> /var/www/html/.env
    done

# ── 2. APP KEY ───────────────────────────────────────────────────────────────
grep -q "APP_KEY=base64:" /var/www/html/.env || php artisan key:generate --force

# ── 3. PERMISOS ──────────────────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── 4. CACHÉ DE LARAVEL ──────────────────────────────────────────────────────
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ── 5. MIGRACIONES ───────────────────────────────────────────────────────────
echo "[start] Corriendo migraciones..."
php artisan migrate --force

# ── 6. LIMPIAR SESIONES VIEJAS (opcional pero recomendado) ───────────────────
# php artisan session:gc   # descomentar si usás file sessions

# ── 7. PHP-FPM ───────────────────────────────────────────────────────────────
echo "[start] Arrancando php-fpm..."
php-fpm --nodaemonize &
PHP_PID=$!

# ── 8. QUEUE WORKER (Livewire broadcasting + jobs) ───────────────────────────
echo "[start] Arrancando queue worker..."
php artisan queue:work \
    --sleep=3 \
    --tries=3 \
    --max-time=3600 \
    --memory=256 \
    --timeout=60 \
    --queue=default,livewire-uploads \
    &
QUEUE_PID=$!

# ── 9. NGINX ─────────────────────────────────────────────────────────────────
echo "[start] Arrancando nginx..."

# Trap para bajar todo limpio si el contenedor recibe SIGTERM
trap "echo '[start] Apagando...'; kill $PHP_PID $QUEUE_PID; nginx -s quit; exit 0" TERM INT

nginx -g "daemon off;"
