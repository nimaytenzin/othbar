# PHP 8.4 — matches composer.lock (Symfony 8.x needs >=8.4) + required extensions
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    libxml2-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        pdo_mysql \
        mysqli \
        zip \
        pcntl \
        soap \
        gd \
        exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/dev/php-uploads.ini /usr/local/etc/php/conf.d/othbar-uploads.ini

WORKDIR /app
