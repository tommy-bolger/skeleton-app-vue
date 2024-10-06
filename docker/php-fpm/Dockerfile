FROM php:8.3-fpm-alpine3.20

# Set working directory
WORKDIR /var/www

RUN apk update \
    && apk upgrade \
# Install temporary build dependencies
    && apk add --no-cache --virtual .build-dependencies \
        ca-certificates \
        $PHPIZE_DEPS \
        build-base \
        curl-dev \
        libtool \
        libxml2-dev \
        zlib-dev \
# Install dependencies
    && apk add --no-cache \
# Bash shell
        bash \
# ZIP extension dependencies
        libzip-dev \
# GD extension dependencies
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
# International dependencies
        icu-dev \
# Install extensions \
    && docker-php-ext-install opcache pdo_mysql soap exif pcntl bcmath \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(getconf _NPROCESSORS_ONLN) gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
# Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
# Remove the build dependencies
    && apk del -f .build-dependencies \
# Add user for laravel application
    && addgroup -g 1000 -S www \
    && adduser -u 1000 -S www -G www -s /bin/bash

# Change current user to www
USER www

# Start php-fpm server
CMD ["php-fpm"]
