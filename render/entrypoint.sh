#!/bin/sh

set -e

cd /var/www/html

php artisan storage:link || true

php artisan migrate --force

php artisan optimize:clear
php artisan optimize
php artisan view:cache

php-fpm -D

exec nginx -g "daemon off;"
