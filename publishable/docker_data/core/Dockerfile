FROM php:7.4.8-apache

RUN apt-get update && apt-get install -y libzip-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev gettext unzip npm git redis-tools
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash && apt-get install -y nodejs
RUN npm install -g npm
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-configure bcmath --enable-bcmath && docker-php-ext-install pdo_mysql zip gd gettext pcntl bcmath
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis
RUN a2enmod rewrite headers proxy proxy_fcgi socache_shmcb ssl http2 actions expires

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
