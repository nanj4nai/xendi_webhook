FROM php:8.2-apache

WORKDIR /var/www/html

# Use the official Composer image to get the binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Copy both composer files (lock file is important)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY . .

# 🔧 FIX: Create writable folder for invoices
RUN mkdir -p /var/www/html/invoices && chmod -R 777 /var/www/html/invoices

# Enable Apache rewrite module
RUN a2enmod rewrite

EXPOSE 80
