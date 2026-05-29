#!/bin/sh
set -e

echo "[start] Iniciando contenedor Laravel..."

# ══════════════════════════════════════════════════════════════════════════════
# ⚠️  GUARDRAILS: PROTECCIÓN CONTRA COMANDOS DESTRUCTIVOS
# ══════════════════════════════════════════════════════════════════════════════
# Crea un wrapper de 'artisan' que bloquea comandos peligrosos en producción.
# Para ejecutar un comando destructivo, usá:
#   ALLOW_DESTRUCTIVE=yes php artisan migrate:fresh --force
# ──────────────────────────────────────────────────────────────────────────────
cat > /usr/local/bin/artisan << 'ARTISAN_WRAPPER'
#!/bin/sh

DANGEROUS_COMMANDS="migrate:fresh migrate:reset migrate:rollback db:wipe tinker"
COMMAND="$2"

for dangerous in $DANGEROUS_COMMANDS; do
    if [ "$COMMAND" = "$dangerous" ]; then
        if [ "$ALLOW_DESTRUCTIVE" != "yes" ]; then
            echo ""
            echo "  ╔══════════════════════════════════════════════════════╗"
            echo "  ║  🚨  COMANDO BLOQUEADO EN PRODUCCIÓN                ║"
            echo "  ║                                                      ║"
            echo "  ║  '$COMMAND' puede causar PÉRDIDA DE DATOS.          ║"
            echo "  ║                                                      ║"
            echo "  ║  Si sabés lo que hacés, ejecutá:                    ║"
            echo "  ║  ALLOW_DESTRUCTIVE=yes php artisan $COMMAND          ║"
            echo "  ╚══════════════════════════════════════════════════════╝"
            echo ""
            echo "  Comando abortado. No se realizaron cambios."
            echo ""
            exit 1
        else
            echo "  ⚠️  ALLOW_DESTRUCTIVE=yes detectado. Ejecutando '$COMMAND'..."
            echo "  Fecha/hora: $(date)"
        fi
    fi
done

exec php /var/www/html/artisan "$@"
ARTISAN_WRAPPER
chmod +x /usr/local/bin/artisan

# ──────────────────────────────────────────────────────────────────────────────
# Banner de advertencia visible al entrar al contenedor (docker exec -it ...)
# ──────────────────────────────────────────────────────────────────────────────
cat > /etc/profile.d/katrix-warning.sh << 'BANNER'
echo ""
echo "  ╔══════════════════════════════════════════════════════════════╗"
echo "  ║  🚀  KATRIX ECOMMERCE — CONTENEDOR DE PRODUCCIÓN           ║"
echo "  ╠══════════════════════════════════════════════════════════════╣"
echo "  ║  ⛔  COMANDOS BLOQUEADOS (requieren ALLOW_DESTRUCTIVE=yes): ║"
echo "  ║      migrate:fresh  •  migrate:reset  •  db:wipe            ║"
echo "  ║      migrate:rollback  •  tinker                            ║"
echo "  ║                                                              ║"
echo "  ║  ✅  Comandos seguros: migrate, db:seed, cache:clear        ║"
echo "  ║  📁  Storage: /var/www/html/storage/app/public              ║"
echo "  ║  📋  Logs: tail -f /var/www/html/storage/logs/laravel.log   ║"
echo "  ╚══════════════════════════════════════════════════════════════╝"
echo ""
BANNER
chmod +x /etc/profile.d/katrix-warning.sh
# ══════════════════════════════════════════════════════════════════════════════

# ── 1. ENV ───────────────────────────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    touch /var/www/html/.env
fi
printenv | grep -E "^(APP_|DB_|SESSION_|CACHE_|QUEUE_|MAIL_|REDIS_|LIVEWIRE_|MP_)" \
    | while IFS='=' read -r key value; do
        grep -q "^${key}=" /var/www/html/.env || echo "${key}=${value}" >> /var/www/html/.env
    done

# ── 2. APP KEY ────────────────────────────────────────────────────────────────
grep -q "APP_KEY=base64:" /var/www/html/.env || php artisan key:generate --force

# ── 3. PERMISOS ───────────────────────────────────────────────────────────────
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/bootstrap/cache
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

LOCK_FILE="/var/www/html/storage/app/.migrate_lock"
CURRENT_HASH=$(find /var/www/html/database/migrations -name "*.php" | sort | xargs md5sum | md5sum | cut -d' ' -f1)

if [ ! -f "$LOCK_FILE" ] || [ "$(cat $LOCK_FILE)" != "$CURRENT_HASH" ]; then
    echo "[start] Cambios detectados en migraciones, ejecutando..."
    php artisan migrate --force
    echo "$CURRENT_HASH" > "$LOCK_FILE"
    echo "[start] Lock actualizado: $CURRENT_HASH"
else
    echo "[start] Sin cambios en migraciones, saltando."
fi

# ── 5.1 SEEDER DE FAMILIAS ───────────────────────────────────────────────────
echo "[start] Verificando familias..."
FAMILY_COUNT=$(php artisan tinker --execute="echo App\Models\Family::count();" 2>/dev/null | tail -1)

if [ "$FAMILY_COUNT" = "0" ] || [ -z "$FAMILY_COUNT" ]; then
    echo "[start] No hay familias, corriendo FamilySeeder..."
    php artisan db:seed --class=FamilySeeder --force
    echo "[start] FamilySeeder completado."
else
    echo "[start] Ya existen $FAMILY_COUNT familias, saltando seeder."
fi

# ── 5.2 SEEDER DE PRODUCTOS ──────────────────────────────────────────────────
echo "[start] Verificando productos..."
PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\Product::count();" 2>/dev/null | tail -1)

if [ "$PRODUCT_COUNT" = "0" ] || [ -z "$PRODUCT_COUNT" ]; then
    echo "[start] No hay productos, corriendo seeder inicial..."
    php artisan db:seed --force || echo "[start] ⚠️  Seeder falló, continuando..."
fi

# Siempre sincronizar productos e imágenes desde Fake Store API.
# El comando es idempotente: usa updateOrCreate en DB y salta imágenes ya descargadas.
# Esto asegura que en cada nuevo deploy las imágenes estén en el volumen de storage.
# IMPORTANTE: || true para que un fallo externo (API caída) no aborte el contenedor.
echo "[start] Sincronizando productos e imágenes desde Fake Store API..."
php artisan import:fakestore || echo "[start] ⚠️  import:fakestore falló (API externa), continuando..."
echo "[start] Sincronización completada."

# ── 5.2.5 ROLES Y SUPERADMIN ─────────────────────────────────────────────────────────
echo "[start] Asegurando roles y cuenta Super Admin..."
php artisan db:seed --class=RolePermissionSeeder --force || echo "[start] ⚠️  RolePermissionSeeder falló"
php artisan db:seed --class=SuperAdminSeeder --force || echo "[start] ⚠️  SuperAdminSeeder falló"
echo "[start] Roles y Super Admin validados."

# ── 5.2.6 TENANT ADMIN POR DEFECTO (con módulos y chatbot) ───────────────────
echo "[start] Asegurando tenant admin y módulos..."
php artisan db:seed --class=TenantSeeder --force || echo "[start] ⚠️  TenantSeeder falló"
echo "[start] Tenant admin y módulos validados."

# ── 5.3 STORAGE LINK ─────────────────────────────────────────────────────────
echo "[start] Creando storage link..."
# Eliminar el symlink/carpeta existente para evitar que storage:link falle en Docker
if [ -L /var/www/html/public/storage ]; then
    echo "[start] Eliminando symlink antiguo..."
    rm /var/www/html/public/storage
elif [ -d /var/www/html/public/storage ]; then
    echo "[start] Eliminando carpeta 'public/storage' residual del build..."
    rm -rf /var/www/html/public/storage
fi
php artisan storage:link --force
echo "[start] Storage link creado: $(ls -la /var/www/html/public/storage)"

# ── 5.4 LIVEWIRE ASSETS ──────────────────────────────────────────────────────
echo "[start] Publicando assets de Livewire..."
php artisan livewire:publish --assets || true

# ── 6. PHP-FPM con watchdog ───────────────────────────────────────────────────
echo "[start] Arrancando php-fpm..."
pkill -9 php-fpm 2>/dev/null || true
rm -f /var/run/php-fpm.pid
sleep 1

(
    while true; do
        php-fpm --nodaemonize || true
        echo "[watchdog] php-fpm cayó, reiniciando en 2s..."
        pkill -9 php-fpm 2>/dev/null || true
        rm -f /var/run/php-fpm.pid
        sleep 2
    done
) &
FPM_WATCHDOG_PID=$!

sleep 2

# ── 7. QUEUE WORKER ───────────────────────────────────────────────────────────
echo "[start] Arrancando queue worker..."
(
    while true; do
        php artisan queue:work \
            --sleep=3 \
            --tries=3 \
            --max-time=3600 \
            --memory=256 \
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
