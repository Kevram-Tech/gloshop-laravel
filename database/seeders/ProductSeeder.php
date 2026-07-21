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
        // Delete all products first (can't use truncate due to foreign key constraints)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::query()->delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
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
                'name' => 'Robe midi fluide — Motifs colorés',
                'slug' => 'robe-midi-fluide-motifs-colores',
                'description' => 'Robe midi légère aux motifs vibrants. Coupe fluide, confortable pour le quotidien comme pour les sorties. Une pièce polyvalente qui s’adapte à tous les styles.',
                'price' => 45000,
                'discount_price' => 38000,
                'sku' => 'ROBE-MIDI-001',
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
                'name' => 'Ensemble tunique & pantalon — Coupe moderne',
                'slug' => 'ensemble-tunique-pantalon-coupe-moderne',
                'description' => 'Ensemble élégant composé d’une tunique ample et d’un pantalon assorti. Tissu respirant, silhouette contemporaine — idéal bureau ou week-end.',
                'price' => 55000,
                'discount_price' => null,
                'sku' => 'ENS-TUN-001',
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
                'name' => 'Jupe crayon — Imprimé contemporain',
                'slug' => 'jupe-crayon-imprime-contemporain',
                'description' => 'Jupe crayon ajustée aux motifs graphiques. Coupe élégante pour le travail ou une soirée. Facile à associer avec un top uni.',
                'price' => 25000,
                'discount_price' => 20000,
                'sku' => 'JUP-IMP-001',
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
                'name' => 'Top manches courtes — Motifs géométriques',
                'slug' => 'top-manches-courtes-motifs-geometriques',
                'description' => 'Top moderne aux motifs géométriques. Coupe nette, manches courtes — une base stylée pour composer vos looks du quotidien.',
                'price' => 18000,
                'discount_price' => null,
                'sku' => 'TOP-GEO-001',
                'stock' => 25,
                'images' => [
                    'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=800&h=800&fit=crop',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Orange et Noir', 'Bleu et Blanc', 'Rouge et Jaune'],
                'is_featured' => false,
                'is_active' => true,
            ],

            // Chaussures
            [
                'category_id' => $chaussures?->id ?? 2,
                'name' => 'Sandales en cuir — Confort quotidien',
                'slug' => 'sandales-cuir-confort-quotidien',
                'description' => 'Sandales en cuir souple, semelle confortable. Design épuré qui s’accorde avec presque toutes les tenues, de la ville aux week-ends.',
                'price' => 35000,
                'discount_price' => 30000,
                'sku' => 'SAND-CUIR-001',
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
                'name' => 'Escarpins — Talon moyen',
                'slug' => 'escarpins-talon-moyen',
                'description' => 'Escarpins élégants à talon moyen pour tenir toute la journée. Une touche de style pour compléter une tenue sophistiquée.',
                'price' => 42000,
                'discount_price' => null,
                'sku' => 'ESCARP-001',
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
                'name' => 'Foulard grand format — Imprimé vif',
                'slug' => 'foulard-grand-format-imprime-vif',
                'description' => 'Grand foulard polyvalent : écharpe, accessoire cheveux ou détail de look. Coton doux, motifs colorés qui dynamisent une tenue simple.',
                'price' => 12000,
                'discount_price' => 10000,
                'sku' => 'FOUL-001',
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
                'name' => 'Ceinture large — Boucle métallique',
                'slug' => 'ceinture-large-boucle-metallique',
                'description' => 'Ceinture large ajustable avec boucle métallique. Structure une silhouette et ajoute du caractère à une robe ou un jean.',
                'price' => 15000,
                'discount_price' => null,
                'sku' => 'CEINT-001',
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
                'name' => 'Sac à main structuré — Quotidien',
                'slug' => 'sac-main-structure-quotidien',
                'description' => 'Sac à main pratique avec doublure et plusieurs compartiments. Design contemporain pour accompagner vos journées en ville.',
                'price' => 38000,
                'discount_price' => 32000,
                'sku' => 'SAC-MAIN-001',
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
                'name' => 'Pochette soirée — Fermeture zip',
                'slug' => 'pochette-soiree-fermeture-zip',
                'description' => 'Pochette compacte pour les sorties. Fermeture à zip, format idéal pour l’essentiel. Un accessoire coloré qui sublime un look simple.',
                'price' => 18000,
                'discount_price' => null,
                'sku' => 'POCH-001',
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
                'name' => 'Collier perles — Longueur ajustable',
                'slug' => 'collier-perles-longueur-ajustable',
                'description' => 'Collier en perles colorées, longueur ajustable. Une pièce signature pour rehausser un col rond ou un décolleté.',
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
                'name' => 'Boucles d’oreilles bois — Design léger',
                'slug' => 'boucles-oreilles-bois-design-leger',
                'description' => 'Boucles d’oreilles en bois, légères et confortables. Un détail naturel qui apporte du caractère sans alourdir le look.',
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
                'name' => 'Bracelet perles — Élastique',
                'slug' => 'bracelet-perles-elastique',
                'description' => 'Bracelet élastique en perles colorées. Facile à porter seul ou empilé — une touche vive pour tous les jours.',
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
                'name' => 'Beurre de karité — Pur',
                'slug' => 'beurre-karite-pur',
                'description' => 'Beurre de karité 100 % pur pour hydrater intensément peau et cheveux. Soin multi-usages, texture riche, format pratique.',
                'price' => 12000,
                'discount_price' => 10000,
                'sku' => 'KARITE-001',
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
                'name' => 'Huile de coco — Vierge',
                'slug' => 'huile-coco-vierge',
                'description' => 'Huile de coco vierge multi-usages : soins capillaires, hydratation corporelle, massage. Formule naturelle, sans additifs.',
                'price' => 15000,
                'discount_price' => null,
                'sku' => 'COCO-001',
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
