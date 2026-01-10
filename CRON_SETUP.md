# Configuration du Scheduler Laravel (Cron)

Le scheduler Laravel est configuré pour s'exécuter automatiquement dans un conteneur Docker dédié.

## Architecture

- **Conteneur `scheduler`** : Exécute `php artisan schedule:run` toutes les minutes
- Utilise la même image PHP que l'application mais en mode CLI
- Partage les mêmes volumes et variables d'environnement que l'application

## Démarrage

Le conteneur scheduler démarre automatiquement avec les autres conteneurs :

```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Vérification

### Vérifier que le scheduler fonctionne

```bash
# Voir les logs du scheduler
docker logs gloshop_laravel_scheduler

# Voir les logs en temps réel
docker logs -f gloshop_laravel_scheduler

# Vérifier que le processus est actif
docker exec gloshop_laravel_scheduler ps aux | grep schedule
```

### Tester manuellement

```bash
# Exécuter le scheduler manuellement
docker exec gloshop_laravel_scheduler php artisan schedule:run

# Voir les tâches planifiées
docker exec gloshop_laravel_scheduler php artisan schedule:list
```

## Configuration des tâches

Les tâches sont définies dans `app/Console/Kernel.php` :

```php
protected function schedule(Schedule $schedule): void
{
    // Exemple : Exécuter une commande toutes les heures
    $schedule->command('your:command')->hourly();
    
    // Exemple : Exécuter une commande tous les jours à minuit
    $schedule->command('your:command')->daily();
    
    // Exemple : Exécuter une commande toutes les 5 minutes
    $schedule->command('your:command')->everyFiveMinutes();
}
```

## Commandes utiles

### Redémarrer le scheduler

```bash
docker-compose -f docker-compose.prod.yml restart scheduler
```

### Arrêter le scheduler

```bash
docker-compose -f docker-compose.prod.yml stop scheduler
```

### Reconstruire le scheduler

```bash
docker-compose -f docker-compose.prod.yml build --no-cache scheduler
docker-compose -f docker-compose.prod.yml up -d scheduler
```

### Voir les logs

```bash
# Dernières 100 lignes
docker logs --tail 100 gloshop_laravel_scheduler

# En temps réel
docker logs -f gloshop_laravel_scheduler
```

## Dépannage

### Le scheduler ne s'exécute pas

1. Vérifier que le conteneur est en cours d'exécution :
   ```bash
   docker ps | grep scheduler
   ```

2. Vérifier les logs :
   ```bash
   docker logs gloshop_laravel_scheduler
   ```

3. Vérifier que les dépendances sont installées :
   ```bash
   docker exec gloshop_laravel_scheduler composer install
   ```

### Les tâches ne s'exécutent pas

1. Vérifier que les tâches sont bien définies dans `app/Console/Kernel.php`

2. Tester manuellement :
   ```bash
   docker exec gloshop_laravel_scheduler php artisan schedule:run -v
   ```

3. Vérifier les logs Laravel :
   ```bash
   docker exec gloshop_laravel_app tail -f storage/logs/laravel.log
   ```

## Alternative : Cron sur l'hôte

Si vous préférez utiliser un cron sur l'hôte au lieu d'un conteneur :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (exécute toutes les minutes)
* * * * * docker exec gloshop_laravel_app php /var/www/html/artisan schedule:run >> /dev/null 2>&1
```

Mais la solution avec le conteneur dédié est recommandée car :
- ✅ Isolation complète
- ✅ Partage des mêmes variables d'environnement
- ✅ Facile à gérer avec Docker Compose
- ✅ Logs centralisés

