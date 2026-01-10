<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    /**
     * Get all active promotions
     */
    public function index(): JsonResponse
    {
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($promotion) {
                return $promotion->isActive();
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $promotions,
        ]);
    }

    /**
     * Get a specific promotion
     */
    public function show(int $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);

        if (!$promotion->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette promotion n\'est plus disponible',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $promotion,
        ]);
    }
}
