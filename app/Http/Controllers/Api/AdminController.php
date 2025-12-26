<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
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
     * Admin login (same as regular login for now)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects',
            ], 401);
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Admin logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::where('payment_status', 'paid')
                ->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_products' => Product::where('is_active', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get all orders (admin)
     */
    public function getOrders(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'orderItems']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        $perPage = $request->get('per_page', 20);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Get order by ID (admin)
     */
    public function getOrder(int $id): JsonResponse
    {
        $order = Order::with(['user', 'orderItems'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour',
            'data' => $order,
        ]);
    }

    /**
     * Get all products (admin)
     */
    public function getProducts(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform images to full URLs
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
     * Get product by ID (admin)
     */
    public function getProduct(int $id): JsonResponse
    {
        $product = Product::with('category')->findOrFail($id);

        // Transform images to full URLs
        $baseUrl = config('app.url', 'http://31.97.185.5:8002');
        if (!empty($product->images)) {
            $product->images = array_map(function ($image) use ($baseUrl) {
                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    return $image;
                }
                if (strpos($image, 'http') !== 0) {
                    $image = ltrim($image, '/');
                    return rtrim($baseUrl, '/') . '/storage/' . $image;
                }
                return $image;
            }, $product->images);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Upload images
     */
    public function uploadImages(Request $request): JsonResponse
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
        ]);

        if (!$request->hasFile('images')) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune image fournie',
            ], 400);
        }

        $uploadedImages = [];
        $baseUrl = config('app.url', 'http://31.97.185.5:8002');

        foreach ($request->file('images') as $image) {
            $filename = 'products/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public', $filename);
            $uploadedImages[] = $filename;
        }

        // Transform to full URLs
        $imageUrls = array_map(function ($image) use ($baseUrl) {
            return rtrim($baseUrl, '/') . '/storage/' . $image;
        }, $uploadedImages);

        return response()->json([
            'success' => true,
            'message' => 'Images uploadées avec succès',
            'data' => [
                'images' => $uploadedImages, // Relative paths for storage
                'image_urls' => $imageUrls, // Full URLs for frontend
            ],
        ]);
    }

    /**
     * Create product (admin)
     */
    public function createProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string', // Can be URLs or file paths
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle file uploads if present
        if ($request->hasFile('image_files')) {
            $uploadedImages = [];
            foreach ($request->file('image_files') as $image) {
                $filename = 'products/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public', $filename);
                $uploadedImages[] = $filename;
            }
            // Merge with existing images if any
            $existingImages = $validated['images'] ?? [];
            $validated['images'] = array_merge($existingImages, $uploadedImages);
        }

        // Generate slug from name
        $validated['slug'] = \Str::slug($validated['name']);

        $product = Product::create($validated);

        // Transform images to full URLs
        if (!empty($product->images)) {
            $product->images = $this->transformImages($product->images);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit créé avec succès',
            'data' => $product,
        ], 201);
    }

    /**
     * Update product (admin)
     */
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'sku' => 'sometimes|string|unique:products,sku,' . $id,
            'stock' => 'sometimes|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle file uploads if present
        if ($request->hasFile('image_files')) {
            $uploadedImages = [];
            foreach ($request->file('image_files') as $image) {
                $filename = 'products/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public', $filename);
                $uploadedImages[] = $filename;
            }
            // Merge with existing images if any
            $existingImages = $validated['images'] ?? [];
            $validated['images'] = array_merge($existingImages, $uploadedImages);
        }

        $product->update($validated);
        $product->refresh();

        // Transform images to full URLs
        if (!empty($product->images)) {
            $product->images = $this->transformImages($product->images);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit mis à jour avec succès',
            'data' => $product,
        ]);
    }

    /**
     * Delete product (admin)
     */
    public function deleteProduct(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé avec succès',
        ]);
    }

    /**
     * Get all categories (admin)
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Create category (admin)
     */
    public function createCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie créée avec succès',
            'data' => $category,
        ], 201);
    }

    /**
     * Update category (admin)
     */
    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $category->fresh(),
        ]);
    }

    /**
     * Delete category (admin)
     */
    public function deleteCategory(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès',
        ]);
    }

    /**
     * Get all users (admin)
     */
    public function getUsers(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get user by ID (admin)
     */
    public function getUser(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Get sales by period (day, week, month)
     */
    public function getSalesByPeriod(): JsonResponse
    {
        $now = now();
        $orders = Order::where('payment_status', 'paid')->get();

        // Sales by Day (last 7 days)
        $salesByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dateKey = $date->format('d/m');
            $dayOrders = $orders->filter(function ($order) use ($date) {
                return $order->created_at->format('Y-m-d') === $date->format('Y-m-d');
            });
            $salesByDay[$dateKey] = (float) $dayOrders->sum('total_amount');
        }

        // Sales by Week (last 4 weeks)
        $salesByWeek = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = $now->copy()->subDays(($i * 7) + 6)->startOfDay();
            $weekEnd = $now->copy()->subDays($i * 7)->endOfDay();
            $weekKey = 'Sem ' . $weekStart->format('d/m');
            $weekOrders = $orders->filter(function ($order) use ($weekStart, $weekEnd) {
                return $order->created_at->between($weekStart, $weekEnd);
            });
            $salesByWeek[$weekKey] = (float) $weekOrders->sum('total_amount');
        }

        // Sales by Month (last 6 months)
        $salesByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthKey = $month->format('M Y');
            $monthOrders = $orders->filter(function ($order) use ($month) {
                return $order->created_at->year == $month->year &&
                       $order->created_at->month == $month->month;
            });
            $salesByMonth[$monthKey] = (float) $monthOrders->sum('total_amount');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'by_day' => $salesByDay,
                'by_week' => $salesByWeek,
                'by_month' => $salesByMonth,
            ],
        ]);
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts(): JsonResponse
    {
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.id',
                'products.name as product_name',
                'products.sku',
                'products.images',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.images', 'products.price')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $images = $item->images ? json_decode($item->images, true) : [];
                $firstImage = !empty($images) ? $images[0] : null;
                
                // Transform image URL if needed
                if ($firstImage && !filter_var($firstImage, FILTER_VALIDATE_URL)) {
                    $baseUrl = config('app.url', 'http://31.97.185.5:8002');
                    $firstImage = rtrim($baseUrl, '/') . '/storage/' . ltrim($firstImage, '/');
                }
                
                return [
                    'product_id' => $item->id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'product_image' => $firstImage,
                    'quantity_sold' => (int) $item->total_quantity,
                    'revenue' => (float) $item->total_revenue,
                    'unit_price' => (float) $item->price,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $topProducts,
        ]);
    }

    /**
     * Get stock statistics
     */
    public function getStockStatistics(): JsonResponse
    {
        $products = Product::all();
        
        $totalStockValue = $products->sum(function ($product) {
            return $product->price * $product->stock;
        });

        $stockByProduct = $products->map(function ($product) {
            $images = $product->images ?? [];
            $firstImage = !empty($images) && is_array($images) ? $images[0] : null;
            
            // Transform image URL if needed
            if ($firstImage && !filter_var($firstImage, FILTER_VALIDATE_URL)) {
                $baseUrl = config('app.url', 'http://31.97.185.5:8002');
                $firstImage = rtrim($baseUrl, '/') . '/storage/' . ltrim($firstImage, '/');
            }

            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $firstImage,
                'stock' => $product->stock,
                'unit_price' => (float) $product->price,
                'total_value' => (float) ($product->price * $product->stock),
                'sku' => $product->sku,
            ];
        })->sortByDesc('total_value')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'total_stock_value' => (float) $totalStockValue,
                'total_products' => $products->count(),
                'total_units' => $products->sum('stock'),
                'by_product' => $stockByProduct,
            ],
        ]);
    }
}
