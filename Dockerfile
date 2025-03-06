# Use PHP 8.2 with FPM
FROM php:8.2-fpm

# Install dependencies (including Nginx)
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage & cache
RUN mkdir -p storage bootstrap/cache && chmod -R 777 storage bootstrap/cache

# Copy Nginx configuration
COPY default.conf /etc/nginx/conf.d/default.conf

# Remove any existing default Nginx configuration
RUN rm -f /etc/nginx/conf.d/default.conf

# Expose ports
EXPOSE 80

# Install Supervisor to manage processes
RUN apt-get update && apt-get install -y supervisor

# Copy Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start both PHP-FPM & Nginx
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
