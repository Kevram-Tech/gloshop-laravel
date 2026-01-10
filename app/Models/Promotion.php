<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'type',
        'discount_value',
        'min_purchase_amount',
        'start_date',
        'end_date',
        'is_active',
        'applicable_categories',
        'applicable_products',
        'usage_limit',
        'usage_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    /**
     * Check if promotion is currently active
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->is_active 
            && $this->start_date <= $now 
            && $this->end_date >= $now
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Calculate discount for a given amount
     */
    public function calculateDiscount(float $amount): float
    {
        if (!$this->isActive() || $amount < ($this->min_purchase_amount ?? 0)) {
            return 0;
        }

        switch ($this->type) {
            case 'percentage':
                return ($amount * $this->discount_value) / 100;
            case 'fixed':
                return min($this->discount_value, $amount);
            case 'free_shipping':
                return 0; // Free shipping is handled separately
            default:
                return 0;
        }
    }
}
