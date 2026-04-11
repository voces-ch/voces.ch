#!/bin/bash
# Exit immediately if any command fails
set -e

echo "Starting deployment scripts..."

# Run database migrations (the --force flag is required in production)
php artisan migrate --force

# Cache configuration, routes, and views for a massive performance boost
php artisan optimize

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Deployment scripts finished. Starting Apache..."
# Hand off control to the main Docker command (Apache)
exec "$@"
