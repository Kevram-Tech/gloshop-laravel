<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Vêtements',
                'slug' => 'vetements',
                'description' => 'Collection complète de vêtements pour femmes : robes, tops, pantalons, jupes et plus encore.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Chaussures',
                'slug' => 'chaussures',
                'description' => 'Chaussures élégantes et confortables : talons, baskets, sandales, bottes pour tous les styles.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Accessoires',
                'slug' => 'accessoires',
                'description' => 'Accessoires de mode : écharpes, ceintures, chapeaux, lunettes de soleil et plus.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Sacs à main',
                'slug' => 'sacs-a-main',
                'description' => 'Sacs à main tendance : sacs à bandoulière, sacs à dos, pochettes et sacs de soirée.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Bijoux',
                'slug' => 'bijoux',
                'description' => 'Bijoux élégants : colliers, bracelets, boucles d\'oreilles, bagues et montres.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Beauté & Cosmétiques',
                'slug' => 'beaute-cosmetiques',
                'description' => 'Produits de beauté et cosmétiques : maquillage, soins de la peau, parfums et plus.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Lingerie',
                'slug' => 'lingerie',
                'description' => 'Lingerie confortable et élégante : soutiens-gorge, culottes, pyjamas et plus.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Maillots de bain',
                'slug' => 'maillots-de-bain',
                'description' => 'Maillots de bain tendance : bikinis, maillots une pièce et accessoires de plage.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Sportswear',
                'slug' => 'sportswear',
                'description' => 'Vêtements de sport pour femmes : leggings, tops de sport, brassières de sport et plus.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Montres',
                'slug' => 'montres',
                'description' => 'Montres élégantes et modernes pour tous les styles et occasions.',
                'image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Catégories créées avec succès!');
    }
}

