#!/bin/sh
set -e

# Pastikan direktori storage & cache tersedia dengan permission penuh
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

# Pastikan symlink storage terhubung
php artisan storage:link --force || true

# Discover packages
php artisan package:discover --ansi || true

# Optimasi route & view jika APP_KEY ada
if [ -n "$APP_KEY" ]; then
    php artisan config:clear || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan FrankenPHP Web Server
exec docker-php-entrypoint --config /etc/caddy/Caddyfile
