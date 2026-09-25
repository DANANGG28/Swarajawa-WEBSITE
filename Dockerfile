# syntax=docker/dockerfile:1

# =============================================================================
# Stage 1 — Build aset frontend (Vite + Tailwind)
# =============================================================================
FROM node:22-bookworm-slim AS assets

WORKDIR /build

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build


# =============================================================================
# Stage 2 — Install dependensi PHP (Composer)
# =============================================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative


# =============================================================================
# Stage 3 — Runtime: PHP-FPM 8.4 + Nginx + Python edge-tts
# =============================================================================
FROM php:8.4-fpm-bookworm

ENV DEBIAN_FRONTEND=noninteractive \
    PORT=80 \
    PATH="/opt/edge-tts/bin:${PATH}"

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        gettext-base \
        python3 \
        python3-pip \
        python3-venv \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        zip \
        unzip \
        git \
        curl \
        ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        intl \
        exif \
        pcntl \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# --- edge-tts (Text-to-Speech Bahasa Jawa, tanpa API key) --------------------
RUN python3 -m venv /opt/edge-tts \
    && /opt/edge-tts/bin/pip install --no-cache-dir --upgrade pip \
    && /opt/edge-tts/bin/pip install --no-cache-dir edge-tts

# --- Konfigurasi PHP & Nginx -------------------------------------------------
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && rm -f /etc/nginx/sites-enabled/default

WORKDIR /var/www/html

# --- Source aplikasi ---------------------------------------------------------
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /build/public/build ./public/build

# Seed storage (korpus RAG & foto) supaya bisa di-restore ke volume kosong.
RUN mkdir -p /opt/app-seed \
    && cp -a storage/app/corpus /opt/app-seed/corpus \
    && cp -a storage/image /opt/app-seed/image \
    && mkdir -p storage/framework/cache/data storage/framework/sessions \
        storage/framework/testing storage/framework/views storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && rm -f public/hot \
    && rm -f bootstrap/cache/*.php

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -fsS "http://127.0.0.1:${PORT}/up" || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
