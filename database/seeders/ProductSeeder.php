<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les catégories
        $vetements = Category::where('slug', 'vetements')->first();
        $chaussures = Category::where('slug', 'chaussures')->first();
        $accessoires = Category::where('slug', 'accessoires')->first();
        $sacs = Category::where('slug', 'sacs-a-main')->first();
        $bijoux = Category::where('slug', 'bijoux')->first();
        $beaute = Category::where('slug', 'beaute-cosmetiques')->first();

        $products = [
            // Vêtements
            [
                'category_id' => $vetements?->id ?? 1,
                'name' => 'Robe en Pagne Togolais - Motif Kente',
                'slug' => 'robe-pagne-togolais-motif-kente',
                'description' => 'Magnifique robe confectionnée en pagne authentique du Togo avec le motif Kente traditionnel. Cette robe élégante allie modernité et tradition africaine. Parfaite pour les occasions spéciales et les événements culturels.',
                'price' => 45000,
                'discount_price' => 38000,
                'sku' => 'ROBE-KENTE-001',
                'stock' => 15,
                'images' => [
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=800&h=800&fit=crop',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Rouge et Or', 'Vert et Jaune', 'Bleu et Blanc'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $vetements?->id ?? 1,
                'name' => 'Ensemble Boubou Togolais - Style Moderne',
                'slug' => 'ensemble-boubou-togolais-style-moderne',
                'description' => 'Ensemble boubou élégant inspiré de la mode togolaise, revisité avec un style moderne. Composé d\'une tunique ample et d\'un pantalon assorti. Tissu de qualité supérieure, confortable et respirant.',
                'price' => 55000,
                'discount_price' => null,
                'sku' => 'BOUBOU-TOGO-001',
                'stock' => 12,
                'images' => [
                    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&h=800&fit=crop',
                    'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?w=800&h=800&fit=crop',
                ],
                'sizes' => ['M', 'L', 'XL'],
                'colors' => ['Bleu Royal', 'Vert Émeraude', 'Bordeaux'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $vetements?->id ?? 1,
                'name' => 'Jupe Crayon en Wax Togolais',
                'slug' => 'jupe-crayon-wax-togolais',
                'description' => 'Jupe crayon moderne en tissu wax authentique du Togo. Coupe ajustée et élégante, parfaite pour le bureau ou les sorties. Motifs colorés et vibrants typiques de l\'Afrique de l\'Ouest.',
                'price' => 25000,
                'discount_price' => 20000,
                'sku' => 'JUP-WAX-TOGO-001',
                'stock' => 20,
                'images' => [
                    'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?w=800&h=800&fit=crop',
                ],
                'sizes' => ['S', 'M', 'L'],
                'colors' => ['Multicolore', 'Rouge et Noir', 'Jaune et Vert'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $vetements?->id ?? 1,
                'name' => 'Top en Pagne - Motifs Géométriques',
                'slug' => 'top-pagne-motifs-geometriques',
                'description' => 'Top élégant en pagne avec motifs géométriques traditionnels du Togo. Manches courtes, coupe moderne. Idéal pour créer des looks authentiques et stylés.',
                'price' => 18000,
                'discount_price' => null,
                'sku' => 'TOP-PAGNE-001',
                'stock' => 25,
                'images' => [
                    'https://images.unsplash.com/photo-1594633312681-425c7b7b97ccd1?w=800&h=800&fit=crop',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Orange et Noir', 'Bleu et Blanc', 'Rouge et Jaune'],
                'is_featured' => false,
                'is_active' => true,
            ],

            // Chaussures
            [
                'category_id' => $chaussures?->id ?? 2,
                'name' => 'Sandales Togolaises en Cuir - Artisanat Local',
                'slug' => 'sandales-togolaises-cuir-artisanat',
                'description' => 'Sandales authentiques fabriquées au Togo par des artisans locaux. Cuir de qualité, semelle confortable. Design traditionnel revisité avec une touche moderne.',
                'price' => 35000,
                'discount_price' => 30000,
                'sku' => 'SAND-TOGO-001',
                'stock' => 18,
                'images' => [
                    'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&h=800&fit=crop',
                ],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'colors' => ['Marron', 'Noir', 'Beige'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $chaussures?->id ?? 2,
                'name' => 'Escarpins à Motifs Africains',
                'slug' => 'escarpins-motifs-africains',
                'description' => 'Escarpins élégants avec motifs inspirés de l\'art togolais. Talon moyen, confortables pour toute la journée. Parfaits pour compléter une tenue moderne avec une touche africaine.',
                'price' => 42000,
                'discount_price' => null,
                'sku' => 'ESCARP-AFR-001',
                'stock' => 10,
                'images' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&h=800&fit=crop',
                ],
                'sizes' => ['36', '37', '38', '39', '40'],
                'colors' => ['Noir et Or', 'Rouge et Noir', 'Bleu et Blanc'],
                'is_featured' => false,
                'is_active' => true,
            ],

            // Accessoires
            [
                'category_id' => $accessoires?->id ?? 3,
                'name' => 'Foulard en Pagne Togolais - Grand Format',
                'slug' => 'foulard-pagne-togolais-grand-format',
                'description' => 'Grand foulard en pagne authentique du Togo. Motifs colorés et vibrants. Polyvalent : peut être porté comme écharpe, turban ou accessoire de cheveux. 100% coton.',
                'price' => 12000,
                'discount_price' => 10000,
                'sku' => 'FOUL-PAGNE-001',
                'stock' => 30,
                'images' => [
                    'https://images.unsplash.com/photo-1601925260368-ae2f83d34e48?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Multicolore', 'Rouge et Jaune', 'Bleu et Vert'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $accessoires?->id ?? 3,
                'name' => 'Ceinture en Tissu Wax - Large',
                'slug' => 'ceinture-tissu-wax-large',
                'description' => 'Ceinture large en tissu wax du Togo avec boucle métallique. Ajoute une touche d\'authenticité à toute tenue. Ajustable, confortable et stylée.',
                'price' => 15000,
                'discount_price' => null,
                'sku' => 'CEINT-WAX-001',
                'stock' => 22,
                'images' => [
                    'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Rouge et Noir', 'Vert et Jaune', 'Bleu et Blanc'],
                'is_featured' => false,
                'is_active' => true,
            ],

            // Sacs
            [
                'category_id' => $sacs?->id ?? 4,
                'name' => 'Sac à Main en Pagne Togolais',
                'slug' => 'sac-main-pagne-togolais',
                'description' => 'Sac à main élégant confectionné en pagne authentique du Togo. Doublure intérieure, plusieurs compartiments. Design unique qui allie tradition et modernité.',
                'price' => 38000,
                'discount_price' => 32000,
                'sku' => 'SAC-PAGNE-001',
                'stock' => 15,
                'images' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Multicolore', 'Rouge et Or', 'Bleu et Blanc'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $sacs?->id ?? 4,
                'name' => 'Pochette en Wax Togolais',
                'slug' => 'pochette-wax-togolais',
                'description' => 'Pochette moderne en tissu wax du Togo. Parfaite pour les sorties. Fermeture à zip, compartiment principal. Design coloré et authentique.',
                'price' => 18000,
                'discount_price' => null,
                'sku' => 'POCH-WAX-001',
                'stock' => 20,
                'images' => [
                    'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Rouge et Noir', 'Vert et Jaune', 'Orange et Bleu'],
                'is_featured' => false,
                'is_active' => true,
            ],

            // Bijoux
            [
                'category_id' => $bijoux?->id ?? 5,
                'name' => 'Collier en Perles Togolaises - Artisanat',
                'slug' => 'collier-perles-togolaises-artisanat',
                'description' => 'Collier authentique en perles artisanales du Togo. Perles colorées traditionnelles, longueur ajustable. Pièce unique fabriquée par des artisans locaux.',
                'price' => 25000,
                'discount_price' => 20000,
                'sku' => 'COLL-PERLE-001',
                'stock' => 12,
                'images' => [
                    'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Multicolore', 'Rouge et Blanc', 'Bleu et Vert'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $bijoux?->id ?? 5,
                'name' => 'Boucles d\'Oreilles en Bois Togolais',
                'slug' => 'boucles-oreilles-bois-togolais',
                'description' => 'Boucles d\'oreilles élégantes en bois sculpté du Togo. Design traditionnel, légères et confortables. Parfaites pour ajouter une touche d\'authenticité à votre style.',
                'price' => 15000,
                'discount_price' => null,
                'sku' => 'BOUC-BOIS-001',
                'stock' => 18,
                'images' => [
                    'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Bois Naturel', 'Bois Teinté'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $bijoux?->id ?? 5,
                'name' => 'Bracelet en Perles Africaines',
                'slug' => 'bracelet-perles-africaines',
                'description' => 'Bracelet coloré en perles africaines traditionnelles. Élastique ajustable, confortable à porter. Design vibrant inspiré de la culture togolaise.',
                'price' => 8000,
                'discount_price' => 6500,
                'sku' => 'BRAC-PERLE-001',
                'stock' => 35,
                'images' => [
                    'https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=800&h=800&fit=crop',
                ],
                'sizes' => ['Unique'],
                'colors' => ['Multicolore', 'Rouge et Jaune', 'Bleu et Blanc'],
                'is_featured' => false,
                'is_active' => true,
            ],

            // Beauté
            [
                'category_id' => $beaute?->id ?? 6,
                'name' => 'Beurre de Karité du Togo - Pur',
                'slug' => 'beurre-karite-togo-pur',
                'description' => 'Beurre de karité 100% pur et naturel du Togo. Produit par des coopératives locales. Hydratant intense pour la peau et les cheveux. Emballage éco-responsable.',
                'price' => 12000,
                'discount_price' => 10000,
                'sku' => 'KARITE-TOGO-001',
                'stock' => 40,
                'images' => [
                    'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=800&h=800&fit=crop',
                ],
                'sizes' => ['250g', '500g'],
                'colors' => ['Naturel'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $beaute?->id ?? 6,
                'name' => 'Huile de Coco Togolaise - Bio',
                'slug' => 'huile-coco-togolaise-bio',
                'description' => 'Huile de coco vierge bio produite au Togo. Multi-usages : soin capillaire, hydratation corporelle, massage. 100% naturelle, sans additifs.',
                'price' => 15000,
                'discount_price' => null,
                'sku' => 'COCO-TOGO-001',
                'stock' => 30,
                'images' => [
                    'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=800&h=800&fit=crop',
                ],
                'sizes' => ['250ml', '500ml'],
                'colors' => ['Naturel'],
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            // Générer le slug si nécessaire
            if (empty($product['slug'])) {
                $product['slug'] = Str::slug($product['name']);
            }

            // Vérifier que le slug est unique
            $slug = $product['slug'];
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $product['slug'] . '-' . $counter;
                $counter++;
            }
            $product['slug'] = $slug;

            Product::create($product);
        }

        $this->command->info('Produits créés avec succès!');
    }
}

