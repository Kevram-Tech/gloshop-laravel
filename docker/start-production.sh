#!/bin/bash
set -e

echo "Starting Laravel application in production mode..."

# Wait for database to be ready (if using external DB, this will fail gracefully)
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection..."
    until php artisan db:show --quiet 2>/dev/null; do
        echo "Database is unavailable - sleeping"
        sleep 2
    done
    echo "Database is up - executing commands"
fi

# Clear and cache configuration
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure storage link exists
php artisan storage:link || true

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start Apache in foreground
exec apache2-foreground

