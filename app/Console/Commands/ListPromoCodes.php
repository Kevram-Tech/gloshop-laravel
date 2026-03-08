<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PromoCode;
use Carbon\Carbon;

class ListPromoCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promo:list {--active : Show only active promo codes} {--expired : Show only expired promo codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Liste tous les codes promo disponibles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = PromoCode::query();

        if ($this->option('active')) {
            $query->where('is_active', true)
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now());
            $this->info('📋 Codes promo actifs:');
        } elseif ($this->option('expired')) {
            $query->where(function ($q) {
                $q->where('is_active', false)
                    ->orWhere('end_date', '<', Carbon::now());
            });
            $this->info('📋 Codes promo expirés:');
        } else {
            $this->info('📋 Tous les codes promo:');
        }

        $promoCodes = $query->orderBy('created_at', 'desc')->get();

        if ($promoCodes->isEmpty()) {
            $this->warn('Aucun code promo trouvé.');
            return 0;
        }

        $headers = ['Code', 'Nom', 'Type', 'Réduction', 'Min', 'Utilisations', 'Validité', 'Statut'];
        $rows = [];

        foreach ($promoCodes as $code) {
            $discount = $code->type === 'percentage' 
                ? $code->discount_value . '%' 
                : ($code->type === 'fixed' ? $code->discount_value . ' FCFA' : 'Livraison gratuite');

            $minPurchase = $code->min_purchase_amount 
                ? number_format($code->min_purchase_amount, 0) . ' FCFA' 
                : 'Aucun';

            $usages = $code->usage_limit 
                ? $code->usage_count . '/' . $code->usage_limit 
                : $code->usage_count . '/∞';

            $validity = $code->end_date->format('d/m/Y');
            
            $status = $code->isValid() ? '✅ Actif' : '❌ Expiré';

            $rows[] = [
                $code->code,
                $code->name,
                ucfirst($code->type),
                $discount,
                $minPurchase,
                $usages,
                $validity,
                $status,
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
        $this->info('Total: ' . $promoCodes->count() . ' code(s) promo');

        return 0;
    }
}
