FROM php:7.4-fpm
COPY php.ini /usr/local/etc/php/
COPY init.sql /docker-entrypoint-initdb.d/
RUN docker-php-ext-install pdo_mysql