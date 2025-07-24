FROM php:8.2-apache

# Enable Apache mod_rewrite if needed (optional for routing)
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy all project files to the container
COPY . /var/www/html

# Install dependencies via Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install

# Expose port 80
EXPOSE 80
