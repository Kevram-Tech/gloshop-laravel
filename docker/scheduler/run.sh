#!/bin/sh
set -e

# Script pour exécuter le scheduler Laravel toutes les minutes

while true; do
    php /var/www/html/artisan schedule:run --verbose --no-interaction &
    sleep 60
done

