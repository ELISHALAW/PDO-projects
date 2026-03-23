# Use the official PHP Apache image
FROM php:8.2-apache

# Install the PDO MySQL extension needed for your _base.php
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (useful for clean URLs)
RUN a2enmod rewrite

# Copy your project files into the container
COPY . /var/www/html/

# Set permissions so Apache can read your files
RUN chown -R www-data:www-data /var/www/html