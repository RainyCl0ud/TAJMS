#!/bin/sh
# Install PHP and PHP-FPM
apk add --no-cache php81 php81-fpm

# Start PHP-FPM
php-fpm81 -D

# Start Laravel
php artisan serve --host=0.0.0.0 --port=$PORT
