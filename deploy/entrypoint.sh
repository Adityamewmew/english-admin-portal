#!/bin/sh
set -e

# Pastikan direktori storage & cache tersedia dengan permission penuh
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

# Pastikan symlink storage terhubung
php artisan storage:link --force || true

# Pastikan static assets di bawah /admin (build, images, favicon) terhubung ke root public
mkdir -p public/admin
rm -rf public/admin/build public/admin/images public/admin/favicon
ln -sfn ../build public/admin/build
ln -sfn ../images public/admin/images
ln -sfn ../favicon public/admin/favicon

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
