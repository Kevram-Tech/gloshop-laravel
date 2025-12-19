# Guide pour mettre à jour les images des produits

## Problème : Les images des produits ne s'affichent pas

Les produits dans le seeder ont maintenant des URLs d'images (Unsplash), mais si vous avez déjà des produits dans la base de données, ils n'ont probablement pas d'images.

## Solution 1 : Réexécuter le seeder (recommandé)

⚠️ **ATTENTION** : Cela supprimera tous les produits existants !

```bash
cd d:\gloshop-laravel
php artisan migrate:fresh --seed
```

## Solution 2 : Mettre à jour les produits existants

Si vous voulez garder les produits existants et juste ajouter des images :

```bash
php artisan tinker
```

Puis exécutez :

```php
use App\Models\Product;

// Ajouter des images placeholder à tous les produits sans images
$products = Product::whereNull('images')->orWhere('images', '[]')->get();

$imageUrls = [
    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&h=800&fit=crop',
    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&h=800&fit=crop',
    'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?w=800&h=800&fit=crop',
    'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&h=800&fit=crop',
    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&h=800&fit=crop',
];

foreach ($products as $index => $product) {
    $product->images = [$imageUrls[$index % count($imageUrls)]];
    $product->save();
}

echo "Images ajoutées à " . $products->count() . " produits\n";
```

## Solution 3 : Utiliser vos propres images

### Option A : Stocker les images localement

1. Créer le dossier de stockage :
```bash
php artisan storage:link
```

2. Placer vos images dans `storage/app/public/products/`

3. Mettre à jour les produits avec les chemins :
```php
use App\Models\Product;

$product = Product::find(1);
$product->images = ['products/image1.jpg', 'products/image2.jpg'];
$product->save();
```

L'API transformera automatiquement ces chemins en URLs complètes.

### Option B : Utiliser des URLs externes

Mettez simplement les URLs complètes dans le champ `images` :

```php
use App\Models\Product;

$product = Product::find(1);
$product->images = [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
];
$product->save();
```

## Vérification

Pour vérifier que les images sont bien configurées :

```bash
php artisan tinker
```

```php
use App\Models\Product;

$product = Product::first();
print_r($product->images);
```

Les images devraient être un tableau d'URLs complètes.

## Notes importantes

1. **Format des images** : Le champ `images` doit être un tableau JSON
2. **URLs complètes** : L'API transforme automatiquement les chemins relatifs en URLs complètes
3. **Images multiples** : Chaque produit peut avoir plusieurs images
4. **Images externes** : Les URLs externes (comme Unsplash) fonctionnent directement

