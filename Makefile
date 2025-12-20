.PHONY: help build up down restart logs shell migrate seed fresh test

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Construire les images Docker
	docker-compose build

up: ## Démarrer les conteneurs
	docker-compose up -d

down: ## Arrêter les conteneurs
	docker-compose down

restart: ## Redémarrer les conteneurs
	docker-compose restart

logs: ## Voir les logs
	docker-compose logs -f

shell: ## Accéder au shell du conteneur app
	docker-compose exec app bash

migrate: ## Exécuter les migrations
	docker-compose exec app php artisan migrate

seed: ## Exécuter les seeders
	docker-compose exec app php artisan db:seed

fresh: ## Réinitialiser la base de données
	docker-compose exec app php artisan migrate:fresh --seed

test: ## Exécuter les tests
	docker-compose exec app php artisan test

cache-clear: ## Vider tous les caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

storage-link: ## Créer le lien de stockage
	docker-compose exec app php artisan storage:link

install: ## Installation complète (build + up + migrate + seed)
	docker-compose build
	docker-compose up -d
	sleep 10
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate --seed
	docker-compose exec app php artisan storage:link

