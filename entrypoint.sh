#!/bin/sh

# Set correct permissions for storage and cache
chmod -R 777 /app/storage /app/bootstrap/cache

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground (so Railway doesn't stop it)
nginx -g "daemon off;"
