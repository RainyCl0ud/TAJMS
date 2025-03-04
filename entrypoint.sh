#!/bin/sh

# Set correct permissions
chmod -R 777 /app/storage /app/bootstrap/cache

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g "daemon off;"
