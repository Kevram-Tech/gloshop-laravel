#!/bin/bash

# Script de diagnostic pour erreur 500

echo "=========================================="
echo "🔍 Diagnostic Erreur 500 - GloShop Laravel"
echo "=========================================="
echo ""

CONTAINER_NAME="gloshop_laravel_app"

# 1. Vérifier l'état des conteneurs
echo "1️⃣ État des conteneurs:"
docker ps -a | grep gloshop
echo ""

# 2. Vérifier les logs récents
echo "2️⃣ Logs récents de l'application:"
docker logs --tail 50 $CONTAINER_NAME 2>&1 | tail -20
echo ""

# 3. Vérifier les logs Laravel
echo "3️⃣ Logs Laravel (dernières erreurs):"
docker exec $CONTAINER_NAME tail -50 storage/logs/laravel.log 2>&1 | grep -A 5 -B 5 "ERROR\|Exception\|Fatal" | tail -30
echo ""

# 4. Vérifier les permissions
echo "4️⃣ Vérification des permissions:"
docker exec $CONTAINER_NAME ls -la storage/ bootstrap/cache/ 2>&1 | head -10
echo ""

# 5. Vérifier .env
echo "5️⃣ Vérification de .env:"
docker exec $CONTAINER_NAME test -f .env && echo "✅ .env existe" || echo "❌ .env manquant"
docker exec $CONTAINER_NAME grep -q "APP_KEY=base64:" .env 2>/dev/null && echo "✅ APP_KEY configurée" || echo "❌ APP_KEY manquante"
echo ""

# 6. Vérifier vendor
echo "6️⃣ Vérification de vendor:"
docker exec $CONTAINER_NAME test -d vendor && echo "✅ vendor/ existe" || echo "❌ vendor/ manquant"
docker exec $CONTAINER_NAME test -f vendor/autoload.php && echo "✅ autoload.php existe" || echo "❌ autoload.php manquant"
echo ""

# 7. Tester PHP
echo "7️⃣ Test PHP:"
docker exec $CONTAINER_NAME php -v
echo ""

# 8. Tester Artisan
echo "8️⃣ Test Artisan:"
docker exec $CONTAINER_NAME php artisan --version 2>&1
echo ""

# 9. Vérifier la connexion à la base de données
echo "9️⃣ Test connexion base de données:"
docker exec $CONTAINER_NAME php artisan tinker --execute="try { DB::connection()->getPdo(); echo '✅ Connexion DB OK'; } catch(Exception \$e) { echo '❌ Erreur DB: ' . \$e->getMessage(); }" 2>&1 | tail -3
echo ""

# 10. Vérifier les caches
echo "🔟 Vérification des caches:"
docker exec $CONTAINER_NAME ls -la bootstrap/cache/ 2>&1 | head -5
echo ""

echo "=========================================="
echo "✅ Diagnostic terminé"
echo "=========================================="

