#!/bin/sh

set -e

echo "Checking database connection..."

# Get database credentials from environment variables
DB_HOST=${DB_HOST:-host.docker.internal}
DB_PORT=${DB_PORT:-3306}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

# Wait for external MySQL to be ready (optional check)
echo "Testing connection to external database at $DB_HOST:$DB_PORT..."
for i in $(seq 1 10); do
    if [ -n "$DB_PASSWORD" ]; then
        # With password
        if php -r "try { \$pdo = new PDO('mysql:host=$DB_HOST;port=$DB_PORT', '$DB_USERNAME', '$DB_PASSWORD'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
            echo "Database connection successful!"
            break
        fi
    else
        # Without password
        if php -r "try { \$pdo = new PDO('mysql:host=$DB_HOST;port=$DB_PORT', '$DB_USERNAME'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
            echo "Database connection successful!"
            break
        fi
    fi
    echo "Waiting for database connection... ($i/10)"
    sleep 2
done

# Install composer dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction || true
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || true

# Run seeders if needed
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Running seeders..."
    php artisan db:seed --force || true
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "Application is ready!"

exec "$@"
