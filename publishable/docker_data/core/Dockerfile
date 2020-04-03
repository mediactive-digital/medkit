FROM php:7.4.3-apache

RUN apt-get update && apt-get install -y libzip-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev gettext unzip nodejs npm git redis-tools
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install pdo_mysql zip gd gettext pcntl
RUN a2enmod rewrite headers proxy proxy_fcgi socache_shmcb ssl http2 actions expires

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
