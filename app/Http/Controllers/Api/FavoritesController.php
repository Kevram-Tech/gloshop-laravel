<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index(): JsonResponse
    {
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with('product.category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlistItems,
        ]);
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if product is already in wishlist
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit est déjà dans vos favoris',
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté aux favoris',
            'data' => $wishlist->load('product'),
        ], 201);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy(int $id): JsonResponse
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré des favoris',
        ]);
    }

    /**
     * Check if a product is in the wishlist.
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $exists,
        ]);
    }
}
