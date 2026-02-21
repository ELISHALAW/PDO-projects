# 1. Use the PHP Apache image
FROM php:8.1-apache

# 2. Install PDO MySQL for your database connection
RUN docker-php-ext-install pdo pdo_mysql

# 3. Enable Apache mod_rewrite (important for many PHP apps)
RUN a2enmod rewrite

# 4. Copy your project files into the server
COPY . /var/www/html/

# 5. CONFIGURE PORT 8080: Update Apache to listen on 8080 instead of 80
RUN sed -i 's/80/8080/' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# 6. Set correct permissions for folders (allows image uploads)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# 7. Tell Render/Docker we are using 8080
EXPOSE 8080
