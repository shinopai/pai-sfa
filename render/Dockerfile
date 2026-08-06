# ==========================
# Vite Build
# ==========================
FROM node:22-alpine AS node

WORKDIR /app

# src/ 内の package.json を /app 直下にコピー
COPY src/package*.json ./

RUN npm ci

# src/ 内のコード全体を /app 直下にコピー
COPY src/ .

RUN npm run build

# ==========================
# Composer
# ==========================
FROM composer:2 AS composer

WORKDIR /app

# composer.json 等も含め、src/ 内の全コードを先に /app 直下へコピー
COPY src/ .

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

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

# Composerステージで準備したLaravelアプリ全体をコピー
COPY --from=composer /app /var/www/html

# NodeステージでビルドしたVite成果物をコピー
COPY --from=node /app/public/build /var/www/html/public/build

# 権限変更（WORKDIRが /var/www/html なので相対パスでOK）
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

COPY render/nginx.conf /etc/nginx/sites-available/default

COPY render/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
