# Guide de Résolution - Erreur 500

## Diagnostic Rapide

Exécutez le script de diagnostic sur le VPS :

```bash
cd /var/www/gloshop-laravel
chmod +x docker/diagnose.sh
./docker/diagnose.sh
```

## Solutions Courantes

### 1. Permissions incorrectes

```bash
docker exec gloshop_laravel_app chown -R www-data:www-data /var/www/html/storage
docker exec gloshop_laravel_app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec gloshop_laravel_app chmod -R 775 /var/www/html/storage
docker exec gloshop_laravel_app chmod -R 775 /var/www/html/bootstrap/cache
```

### 2. APP_KEY manquante

```bash
# Copier docker.env vers .env
docker exec gloshop_laravel_app bash -c "cp docker.env .env"

# Générer la clé
docker exec gloshop_laravel_app php artisan key:generate --force

# Extraire et mettre à jour docker.env
APP_KEY=$(docker exec gloshop_laravel_app bash -c "grep '^APP_KEY=' .env | cut -d '=' -f2-")
sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" docker.env

# Redémarrer
docker restart gloshop_laravel_app
```

### 3. Dépendances Composer manquantes

```bash
docker exec gloshop_laravel_app composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
```

### 4. Cache corrompu

```bash
docker exec gloshop_laravel_app php artisan config:clear
docker exec gloshop_laravel_app php artisan cache:clear
docker exec gloshop_laravel_app php artisan route:clear
docker exec gloshop_laravel_app php artisan view:clear

# Supprimer les fichiers de cache
docker exec gloshop_laravel_app rm -f bootstrap/cache/config.php
docker exec gloshop_laravel_app rm -f bootstrap/cache/services.php
docker exec gloshop_laravel_app rm -f bootstrap/cache/packages.php
docker exec gloshop_laravel_app rm -f bootstrap/cache/routes.php
```

### 5. Problème de base de données

```bash
# Vérifier la connexion
docker exec gloshop_laravel_app php artisan tinker --execute="DB::connection()->getPdo();"

# Vérifier les variables d'environnement
docker exec gloshop_laravel_app env | grep DB_
```

### 6. Voir les logs détaillés

```bash
# Logs du conteneur
docker logs gloshop_laravel_app --tail 100

# Logs Laravel
docker exec gloshop_laravel_app tail -100 storage/logs/laravel.log

# Logs Nginx
docker logs gloshop_laravel_nginx --tail 50
```

### 7. Vérifier les erreurs PHP

```bash
# Activer l'affichage des erreurs temporairement
docker exec gloshop_laravel_app php -i | grep display_errors

# Voir les erreurs PHP
docker exec gloshop_laravel_app php artisan tinker --execute="phpinfo();" | grep -i error
```

### 8. Solution complète (tout réparer)

```bash
cd /var/www/gloshop-laravel

# 1. Arrêter les conteneurs
docker-compose -f docker-compose.prod.yml down

# 2. Nettoyer les caches
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# 3. Redémarrer
docker-compose -f docker-compose.prod.yml up -d

# 4. Attendre
sleep 20

# 5. Installer les dépendances
docker exec gloshop_laravel_app composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 6. Configurer .env
docker exec gloshop_laravel_app bash -c "cp docker.env .env"

# 7. Générer la clé si nécessaire
if ! grep -q "APP_KEY=base64:" docker.env; then
  docker exec gloshop_laravel_app php artisan key:generate --force
  APP_KEY=$(docker exec gloshop_laravel_app bash -c "grep '^APP_KEY=' .env | cut -d '=' -f2-")
  sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" docker.env
  docker exec gloshop_laravel_app bash -c "cp docker.env .env"
fi

# 8. Permissions
docker exec gloshop_laravel_app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec gloshop_laravel_app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Optimiser
docker exec gloshop_laravel_app php artisan config:cache
docker exec gloshop_laravel_app php artisan route:cache
docker exec gloshop_laravel_app php artisan view:cache
docker exec gloshop_laravel_app php artisan storage:link

# 10. Redémarrer
docker restart gloshop_laravel_app
```

## Vérification finale

```bash
# Tester l'application
curl -I http://localhost:6500

# Voir les logs en temps réel
docker logs -f gloshop_laravel_app
```

