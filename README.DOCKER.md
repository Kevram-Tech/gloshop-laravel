# Docker Setup pour GloShop Laravel API

Ce guide explique comment dockeriser et lancer l'application Laravel GloShop.

## Prérequis

- Docker Desktop installé et en cours d'exécution
- Docker Compose installé

## Configuration

### 1. Créer le fichier .env

Copiez le fichier `.env.docker.example` vers `.env` et configurez les variables :

```bash
cp .env.docker.example .env
```

Ou créez manuellement un fichier `.env` avec les variables suivantes :

```env
APP_NAME=GloShop
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8002

DB_CONNECTION=mysql
DB_HOST=host.docker.internal
# Pour accéder à localhost depuis Docker:
# - Windows/Mac: utilisez host.docker.internal
# - Linux: utilisez l'IP de l'hôte ou configurez network_mode: "host"
# Pour une DB sur un serveur distant: utilisez l'IP du serveur (ex: 31.97.185.5)
DB_PORT=3306
DB_DATABASE=gloshop
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Installation complète (recommandé)

Utilisez le Makefile pour une installation complète :

```bash
make install
```

Ou manuellement :

```bash
# Construire les images
docker-compose build

# Démarrer les conteneurs
docker-compose up -d

# Attendre que la base de données soit prête (environ 10 secondes)
# Puis générer la clé d'application
docker-compose exec app php artisan key:generate

# Exécuter les migrations et seeders
docker-compose exec app php artisan migrate --seed

# Créer le lien de stockage
docker-compose exec app php artisan storage:link
```

## Commandes Docker

### Démarrer les conteneurs

```bash
docker-compose up -d
```

### Arrêter les conteneurs

```bash
docker-compose down
```

### Voir les logs

```bash
# Tous les services
docker-compose logs -f

# Un service spécifique
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f redis
```

### Exécuter des commandes Artisan

```bash
# Migrations
docker-compose exec app php artisan migrate

# Seeders
docker-compose exec app php artisan db:seed

# Créer le lien de stockage
docker-compose exec app php artisan storage:link

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### Accéder au shell du conteneur

```bash
docker-compose exec app bash
```

## Services disponibles

- **Application Laravel**: http://localhost:8002
- **Redis**: localhost:6379

**Note**: La base de données MySQL est externe (sur le serveur). Configurez `DB_HOST` dans le fichier `.env` pour pointer vers votre serveur de base de données.

## Structure des volumes

- `./` → `/var/www/html` : Code source de l'application
- `redisdata` : Données Redis (persistantes)

**Note**: La base de données MySQL est gérée sur le serveur externe, pas dans Docker.

## Dépannage

### Réinitialiser la base de données

```bash
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

### Reconstruire les images

```bash
docker-compose build --no-cache
docker-compose up -d
```

### Vérifier les permissions

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

## Production

Pour la production, modifiez `docker-compose.yml` :

1. Changez `APP_DEBUG=false` dans `.env`
2. Utilisez des secrets pour les mots de passe
3. Configurez SSL/TLS avec un reverse proxy
4. Utilisez des volumes nommés pour la persistance
5. Configurez des limites de ressources

