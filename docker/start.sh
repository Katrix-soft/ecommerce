#!/bin/sh
set -e

echo "[start] Iniciando contenedor Laravel..."

# ── 1. ENV ───────────────────────────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi
printenv | grep -E "^(APP_|DB_|SESSION_|CACHE_|QUEUE_|MAIL_|REDIS_|LIVEWIRE_)" \
    | while IFS='=' read -r key value; do
        grep -q "^${key}=" /var/www/html/.env || echo "${key}=${value}" >> /var/www/html/.env
    done

# ── 2. APP KEY ────────────────────────────────────────────────────────────────
grep -q "APP_KEY=base64:" /var/www/html/.env || php artisan key:generate --force

# ── 3. PERMISOS ───────────────────────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── 4. CACHÉ DE LARAVEL ───────────────────────────────────────────────────────
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ── 5. MIGRACIONES ────────────────────────────────────────────────────────────
echo "[start] Corriendo migraciones..."

CURRENT_HASH=$(find /var/www/html/database/migrations -name "*.php" | sort | xargs md5sum | md5sum | cut -d' ' -f1)

# Buscar el hash guardado en la DB
SAVED_HASH=$(php artisan tinker --no-interaction --execute="echo \DB::table('migrations_lock')->value('hash') ?? '';" 2>/dev/null || echo "")

if [ "$SAVED_HASH" != "$CURRENT_HASH" ]; then
    echo "[start] Cambios detectados, ejecutando migraciones..."
    php artisan migrate --force
    # Guardar el nuevo hash en DB
    php artisan tinker --no-interaction --execute="
        \DB::statement('CREATE TABLE IF NOT EXISTS migrations_lock (hash VARCHAR(255))');
        \DB::table('migrations_lock')->delete();
        \DB::table('migrations_lock')->insert(['hash' => '$CURRENT_HASH']);
    " 2>/dev/null || true
    echo "[start] Hash actualizado en DB: $CURRENT_HASH"
else
    echo "[start] Sin cambios en migraciones, saltando."
fi

# ── 5.1 STORAGE LINK ─────────────────────────────────────────────────────────
echo "[start] Creando storage link..."
php artisan storage:link --force

# ── 5.2 LIVEWIRE ASSETS ──────────────────────────────────────────────────────
echo "[start] Publicando assets de Livewire..."
php artisan livewire:publish --assets || true

# ── 6. PHP-FPM con watchdog ───────────────────────────────────────────────────
echo "[start] Arrancando php-fpm..."
(
    while true; do
        php-fpm --nodaemonize || true
        echo "[watchdog] php-fpm cayó, reiniciando en 2s..."
        sleep 2
    done
) &
FPM_WATCHDOG_PID=$!

# Esperar a que php-fpm esté listo antes de arrancar nginx
sleep 2

# ── 7. QUEUE WORKER ───────────────────────────────────────────────────────────
echo "[start] Arrancando queue worker..."
(
    while true; do
        php artisan queue:work \
            --sleep=3 \
            --tries=3 \
            --max-time=3600 \
            --memory=128 \
            --timeout=60 \
            --queue=default,livewire-uploads \
            || true
        echo "[watchdog] queue worker cayó, reiniciando en 3s..."
        sleep 3
    done
) &
QUEUE_PID=$!

# ── 8. NGINX ──────────────────────────────────────────────────────────────────
echo "[start] Arrancando nginx..."
trap "echo '[start] Apagando...'; kill $FPM_WATCHDOG_PID $QUEUE_PID 2>/dev/null; nginx -s quit; exit 0" TERM INT

nginx -g "daemon off;"
