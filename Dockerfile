FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www/

# Install Google Client Library and other dependencies
RUN composer require google/apiclient:^2.15.0 --no-scripts
RUN composer install --optimize-autoloader --no-dev --no-scripts
RUN composer dump-autoload --optimize
RUN npm install && npm run build

# Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Create Google credentials directory and set permissions
RUN mkdir -p /var/www/storage/app/google-credentials
RUN chown -R www-data:www-data /var/www/storage/app/google-credentials
RUN chmod -R 755 /var/www/storage/app/google-credentials

# Expose port
EXPOSE 9000

# Create start script
RUN echo '#!/bin/bash\n\
composer dump-autoload --optimize\n\
php artisan migrate --force\n\
php artisan db:seed --force\n\
php artisan storage:link\n\
service nginx start\n\
php-fpm\n' > /var/www/docker-entrypoint.sh \
&& chmod +x /var/www/docker-entrypoint.sh

# Start script
CMD ["/var/www/docker-entrypoint.sh"]
