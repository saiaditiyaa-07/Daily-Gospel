FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    ssl-cert \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
