# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy the application files
COPY . .

# Change ownership of the /var/www/html directory
RUN chown -R www-data:www-data /var/www/html

# Copy custom Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Set environment variable to allow Composer plugins
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy composer.phar to the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Composer dependencies (including dev dependencies)
RUN composer install --no-scripts --optimize-autoloader

# Clear the Symfony cache
RUN php bin/console cache:clear --no-warmup

# Expose the port Apache is running on
EXPOSE 80

# Run the Apache server in the foreground
CMD ["apache2-foreground"]
