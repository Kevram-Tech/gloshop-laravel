.PHONY: help install migrate seed fresh test cache-clear storage-link

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

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
