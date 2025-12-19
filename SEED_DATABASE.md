# Guide pour peupler la base de données

## Problème : Aucun produit dans GloShop

Si vous ne voyez pas de produits dans l'application, c'est probablement parce que la base de données n'a pas été peuplée avec des données initiales.

## Solution : Exécuter les seeders

### Option 1 : Exécuter tous les seeders

```bash
cd d:\gloshop-laravel
php artisan db:seed
```

Cette commande exécutera :
- `CategorySeeder` : Crée les catégories (Vêtements, Chaussures, Accessoires, etc.)
- `ProductSeeder` : Crée les produits de démonstration

### Option 2 : Exécuter les seeders individuellement

```bash
# Créer les catégories
php artisan db:seed --class=CategorySeeder

# Créer les produits
php artisan db:seed --class=ProductSeeder
```

### Option 3 : Réinitialiser et repeupler la base de données

⚠️ **ATTENTION** : Cette commande supprime toutes les données existantes !

```bash
php artisan migrate:fresh --seed
```

## Vérifier que les données sont créées

### Vérifier les catégories

```bash
php artisan tinker
>>> App\Models\Category::count()
```

### Vérifier les produits

```bash
php artisan tinker
>>> App\Models\Product::count()
>>> App\Models\Product::where('is_active', true)->count()
```

## Produits créés par le seeder

Le `ProductSeeder` crée **14 produits** répartis dans les catégories suivantes :

- **Vêtements** (4 produits) : Robes, boubous, jupes, tops
- **Chaussures** (2 produits) : Sandales, escarpins
- **Accessoires** (2 produits) : Foulards, ceintures
- **Sacs à main** (2 produits) : Sacs en pagne, pochettes
- **Bijoux** (3 produits) : Colliers, boucles d'oreilles, bracelets
- **Beauté** (2 produits) : Beurre de karité, huile de coco

## Si les produits ne s'affichent toujours pas

1. **Vérifier que les produits sont actifs** :
   ```bash
   php artisan tinker
   >>> App\Models\Product::where('is_active', false)->update(['is_active' => true]);
   ```

2. **Vérifier la connexion à l'API** :
   - L'URL de l'API est configurée dans `gloshop/lib/services/api_service.dart`
   - Vérifiez que l'URL `http://31.97.185.5:8002/api` est accessible

3. **Vérifier les logs Laravel** :
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Tester l'endpoint directement** :
   ```bash
   curl http://31.97.185.5:8002/api/products
   ```

## Créer des produits manuellement via l'interface admin

Vous pouvez également créer des produits via l'application admin (`gloadmin`) :
1. Connectez-vous en tant qu'administrateur
2. Allez dans "Produits"
3. Cliquez sur "Ajouter un produit"
4. Remplissez les informations et sauvegardez

