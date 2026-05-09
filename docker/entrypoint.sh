#!/bin/sh

# Exit on error
set -e

# Run migrations if database is ready
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Optimize Laravel for production
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
