<?php

/**
 * Script pour vérifier et peupler la base de données
 * Usage: php check_and_seed.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Category;

echo "=== Vérification de la base de données ===\n\n";

// Vérifier les catégories
$categoriesCount = Category::count();
echo "Catégories: $categoriesCount\n";

if ($categoriesCount == 0) {
    echo "⚠️  Aucune catégorie trouvée. Exécution du CategorySeeder...\n";
    Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
    echo "✓ Catégories créées\n";
}

// Vérifier les produits
$productsCount = Product::count();
$activeProductsCount = Product::where('is_active', true)->count();
echo "Produits totaux: $productsCount\n";
echo "Produits actifs: $activeProductsCount\n";

if ($productsCount == 0) {
    echo "⚠️  Aucun produit trouvé. Exécution du ProductSeeder...\n";
    Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    echo "✓ Produits créés\n";
} elseif ($activeProductsCount == 0) {
    echo "⚠️  Aucun produit actif trouvé. Activation des produits...\n";
    Product::query()->update(['is_active' => true]);
    echo "✓ Produits activés\n";
}

// Afficher quelques produits
$products = Product::where('is_active', true)->limit(5)->get();
echo "\n=== Exemples de produits ===\n";
foreach ($products as $product) {
    echo "- {$product->name} (ID: {$product->id}, Slug: {$product->slug})\n";
}

echo "\n✓ Vérification terminée!\n";

