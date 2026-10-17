#!/bin/sh
set -e

# Pastikan symlink storage terhubung
php artisan storage:link --force || true

# Discover packages
php artisan package:discover --ansi || true

# Cache config & route jika APP_KEY sudah diisi di environment
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan FrankenPHP Web Server
exec docker-php-entrypoint --config /etc/caddy/Caddyfile
