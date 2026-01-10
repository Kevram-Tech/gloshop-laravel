#!/bin/bash

# Script de réparation automatique pour erreur 500

set -e

echo "=========================================="
echo "🔧 Réparation Erreur 500 - GloShop Laravel"
echo "=========================================="
echo ""

CONTAINER_NAME="gloshop_laravel_app"
DEPLOY_PATH="${SFTP_ADMIN_DEPLOY_PATH:-/var/www/gloshop-laravel}"

cd $DEPLOY_PATH

# 1. Vérifier que docker.env existe
if [ ! -f docker.env ]; then
    echo "❌ docker.env n'existe pas!"
    if [ -f docker.env.example ]; then
        echo "Création depuis docker.env.example..."
        cp docker.env.example docker.env
        echo "⚠️  IMPORTANT: Configurez docker.env avec vos valeurs!"
    else
        echo "❌ docker.env.example n'existe pas non plus!"
        exit 1
    fi
fi

# 2. Nettoyer les caches
echo "🧹 Nettoyage des caches..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/routes.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# 3. S'assurer que .env existe dans le conteneur
echo "📋 Configuration du fichier .env..."
docker exec $CONTAINER_NAME bash -c "cp docker.env .env 2>/dev/null || true" || true

# 4. Installer les dépendances si nécessaire
echo "📦 Vérification des dépendances Composer..."
if ! docker exec $CONTAINER_NAME test -d vendor 2>/dev/null; then
    echo "Installation des dépendances..."
    docker exec $CONTAINER_NAME composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || {
        echo "Tentative avec --ignore-platform-reqs..."
        docker exec $CONTAINER_NAME composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs
    }
fi

# 5. Générer la clé d'application si nécessaire
echo "🔑 Vérification de la clé d'application..."
if ! grep -q "APP_KEY=base64:" docker.env 2>/dev/null || grep -q "^APP_KEY=$" docker.env; then
    echo "Génération de la clé..."
    docker exec $CONTAINER_NAME bash -c "cp docker.env .env" || true
    docker exec $CONTAINER_NAME php artisan key:generate --force || true
    
    # Extraire et mettre à jour docker.env
    APP_KEY=$(docker exec $CONTAINER_NAME bash -c "grep '^APP_KEY=' .env 2>/dev/null | cut -d '=' -f2-" || echo "")
    if [ ! -z "$APP_KEY" ] && [ "$APP_KEY" != "" ]; then
        if grep -q "^APP_KEY=" docker.env; then
            sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" docker.env
        else
            echo "APP_KEY=$APP_KEY" >> docker.env
        fi
        echo "✅ Clé générée et sauvegardée"
        docker exec $CONTAINER_NAME bash -c "cp docker.env .env" || true
    fi
else
    echo "✅ Clé déjà présente"
    docker exec $CONTAINER_NAME bash -c "cp docker.env .env" || true
fi

# 6. Permissions
echo "🔐 Configuration des permissions..."
docker exec $CONTAINER_NAME chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
docker exec $CONTAINER_NAME chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# 7. Vider les caches Laravel
echo "🗑️  Vidage des caches Laravel..."
docker exec $CONTAINER_NAME php artisan config:clear || true
docker exec $CONTAINER_NAME php artisan cache:clear || true
docker exec $CONTAINER_NAME php artisan route:clear || true
docker exec $CONTAINER_NAME php artisan view:clear || true

# 8. Optimiser pour la production
echo "⚡ Optimisation pour la production..."
docker exec $CONTAINER_NAME php artisan config:cache || true
docker exec $CONTAINER_NAME php artisan route:cache || true
docker exec $CONTAINER_NAME php artisan view:cache || true
docker exec $CONTAINER_NAME php artisan storage:link || true

# 9. Redémarrer les conteneurs
echo "🔄 Redémarrage des conteneurs..."
docker restart $CONTAINER_NAME || true
sleep 5

# 10. Vérification
echo ""
echo "=========================================="
echo "✅ Réparation terminée!"
echo "=========================================="
echo ""
echo "Vérifications:"
echo "- Logs récents:"
docker logs --tail 10 $CONTAINER_NAME 2>&1 | tail -5
echo ""
echo "- Test HTTP:"
curl -I http://localhost:6500 2>&1 | head -3
echo ""
echo "Si l'erreur persiste, consultez les logs:"
echo "  docker logs gloshop_laravel_app"
echo "  docker exec gloshop_laravel_app tail -50 storage/logs/laravel.log"

