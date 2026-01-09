.PHONY: help build-prod up-prod down-prod restart-prod logs-prod shell-prod deploy-prod

# Use docker compose (v2) if available, otherwise docker-compose (v1)
COMPOSE_CMD := $(shell if docker compose version &> /dev/null; then echo "docker compose"; else echo "docker-compose"; fi)

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build-prod: ## Construire les images Docker (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml build

up-prod: ## Démarrer les conteneurs (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml up -d

down-prod: ## Arrêter les conteneurs (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml down

restart-prod: ## Redémarrer les conteneurs (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml restart

logs-prod: ## Voir les logs (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml logs -f

shell-prod: ## Accéder au shell du conteneur app (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app bash

deploy-prod: ## Déployer en production (build + up + optimize)
	@echo "🚀 Déploiement en production..."
	@if [ ! -f docker.env ]; then \
		echo "⚠️  docker.env n'existe pas. Création depuis docker.env.example..."; \
		cp docker.env.example docker.env; \
		echo "✅ docker.env créé. ⚠️  IMPORTANT: Éditez docker.env avec vos valeurs avant de continuer!"; \
		exit 1; \
	fi
	$(COMPOSE_CMD) -f docker-compose.prod.yml build --no-cache app
	$(COMPOSE_CMD) -f docker-compose.prod.yml up -d
	@echo "⏳ Attente du démarrage..."
	sleep 15
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan config:cache
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan route:cache
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan view:cache
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan storage:link || true
	@echo "✅ Déploiement terminé! Application accessible sur le port 6500"

cache-clear-prod: ## Vider tous les caches (production)
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan cache:clear
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan config:clear
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan route:clear
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan view:clear

cache-optimize-prod: ## Optimiser les caches pour la production
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan config:cache
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan route:cache
	$(COMPOSE_CMD) -f docker-compose.prod.yml exec app php artisan view:cache
