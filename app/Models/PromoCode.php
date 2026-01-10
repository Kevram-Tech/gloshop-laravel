<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'is_active',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'applicable_categories',
        'applicable_products',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    /**
     * Get the orders that used this promo code
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the usages of this promo code
     */
    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    /**
     * Check if promo code is currently valid
     */
    public function isValid(): bool
    {
        $now = Carbon::now();
        return $this->is_active 
            && $this->start_date <= $now 
            && $this->end_date >= $now
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Check if user can use this promo code
     */
    public function canBeUsedBy(int $userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->usage_limit_per_user !== null) {
            $userUsageCount = $this->usages()
                ->where('user_id', $userId)
                ->count();
            
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount for a given amount
     */
    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid() || $amount < ($this->min_purchase_amount ?? 0)) {
            return 0;
        }

        $discount = 0;
        switch ($this->type) {
            case 'percentage':
                $discount = ($amount * $this->discount_value) / 100;
                if ($this->max_discount_amount !== null) {
                    $discount = min($discount, $this->max_discount_amount);
                }
                break;
            case 'fixed':
                $discount = min($this->discount_value, $amount);
                break;
            case 'free_shipping':
                $discount = 0; // Free shipping is handled separately
                break;
        }

        return $discount;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(int $userId, int $orderId = null, float $discountAmount = 0): void
    {
        $this->increment('usage_count');
        
        PromoCodeUsage::create([
            'promo_code_id' => $this->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);
    }
}
