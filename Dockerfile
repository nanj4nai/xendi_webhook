FROM php:8.2-apache

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Install git and unzip for composer dependencies
RUN apt-get update && apt-get install -y git unzip

# Set working directory inside container
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Run Composer install (after git and unzip are ready)
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Expose port
EXPOSE 80
