FROM php:8.2-cli

# dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# copiar proyecto
COPY . .

# instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# permisos (storage y cache)
RUN mkdir -p storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Render expone el puerto en $PORT
CMD php artisan config:clear && php artisan route:clear && php -S 0.0.0.0:${PORT} -t public

