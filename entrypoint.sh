#!/bin/sh

# Set correct permissions
chmod -R 777 /app/storage /app/bootstrap/cache

# Start PHP-FPM in the foreground
php-fpm &

# Start Nginx in the foreground
nginx -g "daemon off;"
