# ==========================
# Vite Build
# ==========================
FROM node:22-alpine AS node

WORKDIR /app

COPY src/package*.json ./

RUN npm ci

COPY src/ .

RUN npm run build

# ==========================
# Composer
# ==========================
FROM composer:2 AS composer

WORKDIR /app

COPY src/composer.json src/composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

COPY src/ .

# ==========================
# PHP Runtime
# ==========================
FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    nginx \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /app /var/www/html

COPY --from=node /app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data \
storage \
bootstrap/cache

COPY render/nginx.conf /etc/nginx/sites-available/default

COPY render/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
