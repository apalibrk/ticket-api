# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip && \
    docker-php-ext-install intl mbstring pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy the composer binary from the composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Change ownership of the /var/www/html directory
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public

# Set environment variable to allow Composer plugins
ENV COMPOSER_ALLOW_SUPERUSER=1

# Run composer install without dev dependencies and scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts && \
    php bin/console cache:clear --no-warmup

# Run the Apache server in the foreground
CMD ["apache2-foreground"]
