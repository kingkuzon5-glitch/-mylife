# Build frontend assets
FROM node:20-alpine AS assets

WORKDIR /var/www

COPY package*.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./
COPY public ./public
RUN npm run build

# Application image
FROM php:8.2-fpm

# Install system dependencies, Nginx, and required PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exif bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Copy compiled frontend assets from the build stage
COPY --from=assets /var/www/public/build /var/www/public/build

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port and start Laravel server
EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000
