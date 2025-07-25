FROM php:8.2-apache

WORKDIR /var/www/html

# Use the official Composer image to get the binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Optional: Install PHP extensions (if DomPDF or PHPMailer needs it)
RUN apt-get update && apt-get install -y \
    zip unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql

# Copy both composer files (lock file is important)
COPY composer.json composer.lock ./

# Install dependencies using the lock file
RUN composer install --no-dev --optimize-autoloader

# Copy your app after dependencies are installed
COPY . .

# Enable Apache rewrite module
RUN a2enmod rewrite

EXPOSE 80
