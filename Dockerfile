FROM php:8.2-fpm

# Install system dependencies, Nginx, and PHP extensions needed by Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exim bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port and start Laravel server
EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000
