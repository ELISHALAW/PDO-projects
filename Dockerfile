FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    git \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        gd \
        curl \
        mbstring \
        fileinfo \
        bcmath \
    && a2enmod rewrite \
    && a2enmod headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install Composer and run composer install if composer.json exists
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN if [ -f composer.json ]; then \
      composer install --no-dev --optimize-autoloader --no-interaction 2>&1; \
    fi

# Configure Apache for the application
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/pdo-app.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/pdo-app.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/pdo-app.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/pdo-app.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/pdo-app.conf && \
    a2enconf pdo-app

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} + && \
    find /var/www/html -type f -exec chmod 644 {} + && \
    chmod 755 /var/www/html

# Create directories for uploads if they don't exist
RUN mkdir -p /var/www/html/uploads && \
    mkdir -p /var/www/html/uploaded_img && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploaded_img

# Enable error logging to stderr for Render visibility
RUN sed -i 's|^ErrorLog.*|ErrorLog /proc/self/fd/2|' /etc/apache2/apache2.conf && \
    sed -i 's|^CustomLog.*|CustomLog /proc/self/fd/1 combined|' /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
