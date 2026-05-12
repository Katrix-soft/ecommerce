FROM php:8.2-fpm-alpine

# Dependencias del sistema
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    git \
    zip \
    unzip \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Instalar dependencias PHP
RUN composer install --optimize-autoloader --no-interaction --no-dev

# Instalar dependencias JS y compilar assets
RUN npm install && npm run build && npm cache clean --force

# Permisos (ya están en start.sh pero no está de más tenerlos acá también)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── CONFIGS DOCKER ───────────────────────────────────────────────────────────
# nginx: va en conf.d, NO en nginx.conf (alpine usa include conf.d/*)
COPY docker/nginx.conf /etc/nginx/nginx.conf

# php-fpm pool config (el que controla workers y memoria)
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Script de inicio
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]
