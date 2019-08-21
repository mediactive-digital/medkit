FROM php:7.2-apache

RUN docker-php-ext-install pdo_mysql
RUN a2dismod mpm_prefork
RUN a2enmod rewrite headers mpm_event proxy proxy_fcgi socache_shmcb ssl http2 actions expires
