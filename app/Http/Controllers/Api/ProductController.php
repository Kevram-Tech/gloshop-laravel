<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Transform product images to full URLs.
     */
    private function transformImages($images, $baseUrl = null)
    {
        if (empty($images) || !is_array($images)) {
            return [];
        }

        $baseUrl = $baseUrl ?? config('app.url', 'http://31.97.185.5:8002');
        
        return array_map(function ($image) use ($baseUrl) {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }
            if (strpos($image, 'http') !== 0) {
                $image = ltrim($image, '/');
                return rtrim($baseUrl, '/') . '/storage/' . $image;
            }
            return $image;
        }, $images);
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true)
            ->with('category');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        // Transform products to ensure images are full URLs
        $products->getCollection()->transform(function ($product) {
            if (!empty($product->images)) {
                $product->images = $this->transformImages($product->images);
            }
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        // Get related products from the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Transform images to full URLs
        if (!empty($product->images)) {
            $product->images = $this->transformImages($product->images);
        }

        // Transform related products images
        $relatedProducts->transform(function ($relatedProduct) {
            if (!empty($relatedProduct->images)) {
                $relatedProduct->images = $this->transformImages($relatedProduct->images);
            }
            return $relatedProduct;
        });

        return response()->json([
            'success' => true,
            'data' => $product,
            'related_products' => $relatedProducts,
        ]);
    }
}

