FROM dunglas/frankenphp:1-php8.3

# Set Caddy server name ke "http://" agar serve di port 80 (Dokploy reverse proxy)
ENV SERVER_NAME="http://"
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP esensial untuk Laravel + PostgreSQL
RUN install-php-extensions \
    gd \
    pcntl \
    opcache \
    pdo \
    pdo_pgsql \
    zip \
    redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=oven/bun:latest /usr/local/bin/bun /usr/local/bin/bun

WORKDIR /var/www/html

# 1. Install Composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 2. Install Node/Bun dependencies
COPY package.json bun.lock ./
RUN bun install

# 3. Copy source code aplikasi
COPY . .

# 4. Generate optimized autoloader & build Vite assets
RUN git config --global --add safe.directory /var/www/html \
    && composer dump-autoload --optimize --no-dev --no-scripts
RUN bun run build

# 5. Konfigurasi PHP & Permissions
COPY ./deploy/php.ini /usr/local/etc/php/conf.d/custom.ini
RUN chmod +x ./deploy/entrypoint.sh \
    && mkdir -p public/uploads/certificates \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads

EXPOSE 80

ENTRYPOINT ["./deploy/entrypoint.sh"]