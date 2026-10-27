# syntax=docker/dockerfile:1

# Build Laravel's Vite assets
FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
RUN npm run build

# Run the Laravel API on PHP
FROM php:8.3-cli-bookworm AS app
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libonig-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync

USER www-data

EXPOSE 10000
CMD ["sh", "-c", "touch database/database.sqlite && php artisan migrate --force && exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
