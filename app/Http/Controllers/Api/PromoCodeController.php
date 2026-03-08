<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PromoCodeController extends Controller
{
    /**
     * Validate a promo code
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $promoCode = PromoCode::where('code', strtoupper($request->code))->first();

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Code promo invalide ou inexistant',
            ], 404);
        }

        if (!$promoCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce code promo n\'est plus valide',
            ], 400);
        }

        if (!$promoCode->canBeUsedBy(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez atteint la limite d\'utilisation de ce code promo',
            ], 400);
        }

        if ($request->amount < ($promoCode->min_purchase_amount ?? 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Montant minimum requis: ' . number_format($promoCode->min_purchase_amount, 2) . ' FCFA',
            ], 400);
        }

        $discount = $promoCode->calculateDiscount($request->amount);

        return response()->json([
            'success' => true,
            'data' => [
                'promo_code' => $promoCode,
                'discount_amount' => $discount,
                'final_amount' => $request->amount - $discount,
            ],
        ]);
    }

    /**
     * Get user's available promo codes
     */
    public function available(): JsonResponse
    {
        $promoCodes = PromoCode::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get()
            ->filter(function ($code) {
                return $code->isValid() && $code->canBeUsedBy(Auth::id());
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $promoCodes,
        ]);
    }
}
