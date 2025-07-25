FROM php:8.2-apache

WORKDIR /var/www/html

# Install Composer and dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install

# Copy everything else
COPY . .

# Enable Apache rewrite if needed
RUN a2enmod rewrite
