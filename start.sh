#!/bin/bash

sleep 30 

# Run migrations
php artisan migrate --force

# Link storage
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the server
php artisan serve --host=0.0.0.0 --port=9000