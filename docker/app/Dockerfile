FROM php:8.1.3-fpm
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql

RUN ['chmod 777 -R','/var/www/app']