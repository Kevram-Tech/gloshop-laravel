<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PromoCode;
use Carbon\Carbon;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'BIENVENUE2026',
                'name' => 'Code de bienvenue',
                'description' => 'Code promo de bienvenue pour les nouveaux clients',
                'type' => 'percentage',
                'discount_value' => 15.00,
                'min_purchase_amount' => 5000.00,
                'max_discount_amount' => 2000.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
            ],
            [
                'code' => 'PROMO10',
                'name' => 'Réduction 10%',
                'description' => 'Profitez de 10% de réduction sur votre commande',
                'type' => 'percentage',
                'discount_value' => 10.00,
                'min_purchase_amount' => 2000.00,
                'max_discount_amount' => 1500.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'usage_limit' => 500,
                'usage_limit_per_user' => 3,
                'usage_count' => 0,
            ],
            [
                'code' => 'SOLDES2026',
                'name' => 'Soldes 2026',
                'description' => 'Code promo pour les soldes de 2026',
                'type' => 'percentage',
                'discount_value' => 20.00,
                'min_purchase_amount' => 10000.00,
                'max_discount_amount' => 5000.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'is_active' => true,
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
            ],
            [
                'code' => 'FIDELE500',
                'name' => 'Client fidèle',
                'description' => 'Réduction fixe de 500 FCFA pour nos clients fidèles',
                'type' => 'fixed',
                'discount_value' => 500.00,
                'min_purchase_amount' => 3000.00,
                'max_discount_amount' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYear(),
                'is_active' => true,
                'usage_limit' => null,
                'usage_limit_per_user' => 5,
                'usage_count' => 0,
            ],
            [
                'code' => 'LIVRAISON2026',
                'name' => 'Livraison gratuite',
                'description' => 'Profitez de la livraison gratuite',
                'type' => 'free_shipping',
                'discount_value' => 0.00,
                'min_purchase_amount' => 5000.00,
                'max_discount_amount' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(4),
                'is_active' => true,
                'usage_limit' => 300,
                'usage_limit_per_user' => 2,
                'usage_count' => 0,
            ],
            [
                'code' => 'FLASH25',
                'name' => 'Vente flash',
                'description' => '25% de réduction - Offre limitée',
                'type' => 'percentage',
                'discount_value' => 25.00,
                'min_purchase_amount' => 15000.00,
                'max_discount_amount' => 7500.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addWeeks(2),
                'is_active' => true,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
            ],
            [
                'code' => 'TEST2026',
                'name' => 'Code Test',
                'description' => 'Code promo de test pour le développement',
                'type' => 'percentage',
                'discount_value' => 10.00,
                'min_purchase_amount' => null,
                'max_discount_amount' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(12),
                'is_active' => true,
                'usage_limit' => null,
                'usage_limit_per_user' => null,
                'usage_count' => 0,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::updateOrCreate(
                ['code' => $promoCode['code']],
                $promoCode
            );
        }

        $this->command->info('✅ ' . count($promoCodes) . ' codes promo créés avec succès!');
    }
}
