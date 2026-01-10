.PHONY: help install migrate seed fresh test cache-clear storage-link docker-up docker-down docker-build docker-restart docker-logs docker-shell docker-install docker-fresh

# Détecter docker compose ou docker-compose
COMPOSE_CMD := $(shell docker compose version >/dev/null 2>&1 && echo "docker compose" || echo "docker-compose")

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# ============================================
# Commandes Docker (Développement Local)
# ============================================

docker-build: ## Construire les images Docker
	$(COMPOSE_CMD) build

docker-up: ## Démarrer les conteneurs Docker
	$(COMPOSE_CMD) up -d

docker-down: ## Arrêter les conteneurs Docker
	$(COMPOSE_CMD) down

docker-restart: ## Redémarrer les conteneurs Docker
	$(COMPOSE_CMD) restart

docker-logs: ## Voir les logs des conteneurs
	$(COMPOSE_CMD) logs -f

docker-shell: ## Accéder au shell du conteneur app
	$(COMPOSE_CMD) exec app bash

docker-install: ## Installer les dépendances dans le conteneur
	$(COMPOSE_CMD) exec app composer install

docker-migrate: ## Exécuter les migrations dans le conteneur
	$(COMPOSE_CMD) exec app php artisan migrate

docker-seed: ## Exécuter les seeders dans le conteneur
	$(COMPOSE_CMD) exec app php artisan db:seed

docker-fresh: ## Réinitialiser la base de données dans le conteneur
	$(COMPOSE_CMD) exec app php artisan migrate:fresh --seed

docker-key: ## Générer la clé d'application dans le conteneur
	$(COMPOSE_CMD) exec app php artisan key:generate

docker-cache-clear: ## Vider tous les caches dans le conteneur
	$(COMPOSE_CMD) exec app php artisan cache:clear
	$(COMPOSE_CMD) exec app php artisan config:clear
	$(COMPOSE_CMD) exec app php artisan route:clear
	$(COMPOSE_CMD) exec app php artisan view:clear

docker-storage-link: ## Créer le lien de stockage dans le conteneur
	$(COMPOSE_CMD) exec app php artisan storage:link

# ============================================
# Commandes Locales (Sans Docker)
# ============================================

install: ## Installer les dépendances Composer
	composer install --no-dev --optimize-autoloader

update: ## Mettre à jour les dépendances Composer
	composer update --no-dev --optimize-autoloader

migrate: ## Exécuter les migrations
	php artisan migrate

seed: ## Exécuter les seeders
	php artisan db:seed

fresh: ## Réinitialiser la base de données
	php artisan migrate:fresh --seed

test: ## Exécuter les tests
	php artisan test

cache-clear: ## Vider tous les caches
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

cache-optimize: ## Optimiser les caches pour la production
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache

storage-link: ## Créer le lien de stockage
	php artisan storage:link

key-generate: ## Générer la clé d'application
	php artisan key:generate

optimize: ## Optimisation complète (cache + autoload)
	composer install --no-dev --optimize-autoloader
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan storage:link
