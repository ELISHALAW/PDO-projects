FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libzip-dev zip unzip libpng-dev libonig-dev libxml2-dev git \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && a2enmod rewrite

WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install Composer (copy from official composer image) and install PHP deps
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction; fi

# Ensure web user owns files and typical writable dirs exist
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} +

EXPOSE 80

CMD ["apache2-foreground"]
