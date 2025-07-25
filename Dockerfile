FROM php:8.2-apache

WORKDIR /var/www/html

# Only copy composer.json (skip composer.lock for now)
COPY composer.json ./

RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install

COPY . .

RUN a2enmod rewrite

EXPOSE 80
