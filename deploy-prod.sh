#!/bin/bash

# Production deployment script for GloShop Laravel API

set -e

echo "=========================================="
echo "🚀 Deploying GloShop Laravel API to Production"
echo "=========================================="

# 1. Check prerequisites
if ! command -v docker-compose &> /dev/null && ! command -v docker &> /dev/null; then
    echo "❌ Error: Docker and Docker Compose are not installed."
    exit 1
fi

# Use docker compose (v2) if available, otherwise docker-compose (v1)
COMPOSE_CMD="docker-compose"
if docker compose version &> /dev/null; then
    COMPOSE_CMD="docker compose"
fi

# 2. Clean host cache (critical to avoid 500 errors)
echo "🧹 Cleaning host cache..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/routes.php

# 3. Verify docker.env exists
echo "📋 Checking docker.env file..."
if [ ! -f docker.env ]; then
    echo "⚠️  docker.env does not exist. Creating from docker.env.example..."
    if [ -f docker.env.example ]; then
        cp docker.env.example docker.env
        echo "✅ docker.env created. ⚠️  IMPORTANT: Edit docker.env with your actual values before continuing!"
        exit 1
    else
        echo "❌ Error: docker.env.example does not exist."
        exit 1
    fi
fi
echo "✅ docker.env found"

# 4. Stop existing container
echo "🛑 Stopping existing App container..."
$COMPOSE_CMD -f docker-compose.prod.yml stop app 2>/dev/null || true
$COMPOSE_CMD -f docker-compose.prod.yml rm -f app 2>/dev/null || true

# 5. Build image (force code update)
echo "🏗️ Building Docker image..."
$COMPOSE_CMD -f docker-compose.prod.yml build --no-cache app

# 6. Start container
echo "▶️ Starting App container..."
$COMPOSE_CMD -f docker-compose.prod.yml up -d app

# 7. Wait for container to be ready
echo "⏳ Waiting for container to be ready..."
sleep 10

# 8. Post-deployment: Laravel optimizations
echo "✨ Finalizing (Cache & Config)..."
CONTAINER_NAME="gloshop_laravel_app"

# Wait for container to be healthy
MAX_ATTEMPTS=30
ATTEMPT=0
while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    if docker exec $CONTAINER_NAME php artisan --version &> /dev/null; then
        echo "✅ Container is ready"
        break
    fi
    ATTEMPT=$((ATTEMPT + 1))
    echo "   Waiting... ($ATTEMPT/$MAX_ATTEMPTS)"
    sleep 2
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "⚠️  Warning: Container may not be fully ready, but continuing..."
fi

# Run Laravel optimizations
echo "🔄 Running Laravel optimizations..."
docker exec $CONTAINER_NAME php artisan config:clear || true
docker exec $CONTAINER_NAME php artisan view:clear || true
docker exec $CONTAINER_NAME php artisan route:clear || true
docker exec $CONTAINER_NAME php artisan cache:clear || true

# Cache for production
docker exec $CONTAINER_NAME php artisan config:cache || true
docker exec $CONTAINER_NAME php artisan route:cache || true
docker exec $CONTAINER_NAME php artisan view:cache || true

# Ensure storage link exists
docker exec $CONTAINER_NAME php artisan storage:link || true

# Set proper permissions
docker exec $CONTAINER_NAME chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
docker exec $CONTAINER_NAME chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo ""
echo "=========================================="
echo "✅ Deployment completed successfully!"
echo "=========================================="
echo "Application is accessible via configured domain."
echo "Service status:"
$COMPOSE_CMD -f docker-compose.prod.yml ps
echo ""
echo "Container logs (last 50 lines):"
docker logs --tail 50 $CONTAINER_NAME
echo ""