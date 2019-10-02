FROM php:7.2-apache

RUN apt-get update && apt-get install -y zlib1g-dev gettext
RUN docker-php-ext-install pdo_mysql zip gettext json
RUN a2enmod rewrite headers proxy proxy_fcgi socache_shmcb ssl http2 actions expires
