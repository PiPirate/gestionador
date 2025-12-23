FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev curl \
 && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node (para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

WORKDIR /var/www

COPY . .

# PHP deps
RUN composer install --no-dev --optimize-autoloader

# Frontend build (Vite)
RUN npm ci && npm run build

# permisos
RUN mkdir -p storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# evitar caches viejos
CMD php artisan config:clear && php artisan route:clear && php -S 0.0.0.0:${PORT} -t public
