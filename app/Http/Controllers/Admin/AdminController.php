<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ])->withInput($request->only('email'));
        }

        if (!$user->is_admin) {
            return back()->withErrors([
                'email' => 'Vous n\'avez pas les droits d\'accès à cette zone.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user, $request->filled('remember'));

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_payments' => PaymentTransaction::count(),
            'completed_payments' => PaymentTransaction::where('status', 'completed')->count(),
            'pending_payments' => PaymentTransaction::where('status', 'pending')->count(),
            'total_payment_amount' => PaymentTransaction::where('status', 'completed')->sum('amount'),
        ];

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent payments
        $recentPayments = PaymentTransaction::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentPayments'));
    }

    /**
     * Show products list
     */
    public function products(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show create product form
     */
    public function createProduct()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Show edit product form
     */
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Show orders list
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'orderItems']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show categories list
     */
    public function categories()
    {
        $categories = Category::withCount('products')->orderBy('created_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show users list
     */
    public function users(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a new product
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Update a product
     */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Delete a product
     */
    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Show create category form
     */
    public function createCategory()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Show edit category form
     */
    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Delete a category
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer une catégorie qui contient des produits.');
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Update order status
     */
    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['is_admin'] = $request->has('is_admin');

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Delete a user
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting the last admin
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Show payments list
     */
    public function payments(Request $request)
    {
        $query = PaymentTransaction::with(['order', 'user']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tx_reference', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhere('identifier', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => PaymentTransaction::count(),
            'completed' => PaymentTransaction::where('status', 'completed')->count(),
            'pending' => PaymentTransaction::where('status', 'pending')->count(),
            'failed' => PaymentTransaction::where('status', 'failed')->count(),
            'cancelled' => PaymentTransaction::where('status', 'cancelled')->count(),
            'total_amount' => PaymentTransaction::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details
     */
    public function showPayment($id)
    {
        $payment = PaymentTransaction::with(['order.orderItems.product', 'user'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update payment status
     */
    public function updatePayment(Request $request, $id)
    {
        $payment = PaymentTransaction::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $payment->status;
            $payment->update(['status' => $validated['status']]);

            // Si le paiement est marqué comme complété, mettre à jour la commande
            if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
                $order = $payment->order;
                if ($order && $order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => $order->status === 'pending' ? 'processing' : $order->status,
                    ]);
                }
            }

            // Si le paiement est marqué comme échoué ou annulé, mettre à jour la commande
            if (in_array($validated['status'], ['failed', 'cancelled']) && $oldStatus === 'completed') {
                $order = $payment->order;
                if ($order) {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.payments.show', $payment->id)
                ->with('success', 'Statut du paiement mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.payments.show', $payment->id)
                ->with('error', 'Erreur lors de la mise à jour du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Show platform statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year, all

        // Calculate date range based on period
        $now = Carbon::now();
        switch ($period) {
            case 'day':
                $startDate = $now->copy()->startOfDay();
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                break;
            default:
                $startDate = null;
        }

        // General Statistics
        $generalStats = [
            'total_users' => User::count(),
            'new_users' => $startDate ? User::where('created_at', '>=', $startDate)->count() : User::count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_orders' => $startDate ? Order::where('created_at', '>=', $startDate)->count() : Order::count(),
            'total_revenue' => $startDate 
                ? Order::where('payment_status', 'paid')->where('created_at', '>=', $startDate)->sum('total_amount')
                : Order::where('payment_status', 'paid')->sum('total_amount'),
            'total_payments' => $startDate 
                ? PaymentTransaction::where('created_at', '>=', $startDate)->count()
                : PaymentTransaction::count(),
            'completed_payments' => $startDate
                ? PaymentTransaction::where('status', 'completed')->where('created_at', '>=', $startDate)->count()
                : PaymentTransaction::where('status', 'completed')->count(),
        ];

        // Orders Statistics
        $ordersStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // Payment Statistics
        $paymentStats = [
            'pending' => PaymentTransaction::where('status', 'pending')->count(),
            'completed' => PaymentTransaction::where('status', 'completed')->count(),
            'failed' => PaymentTransaction::where('status', 'failed')->count(),
            'cancelled' => PaymentTransaction::where('status', 'cancelled')->count(),
            'total_amount' => PaymentTransaction::where('status', 'completed')->sum('amount'),
        ];

        // Payment Methods Statistics
        $paymentMethods = PaymentTransaction::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // Top Products (by sales)
        $topProducts = OrderItem::select('product_id', 'product_name', 
                DB::raw('sum(quantity) as total_quantity'),
                DB::raw('sum(price * quantity) as total_revenue'))
            ->whereHas('order', function($query) use ($startDate) {
                $query->where('payment_status', 'paid');
                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Category Statistics
        $categoryStats = Category::withCount('products')
            ->get()
            ->map(function($category) use ($startDate) {
                $sales = OrderItem::whereHas('product', function($q) use ($category) {
                    $q->where('category_id', $category->id);
                })
                ->whereHas('order', function($query) use ($startDate) {
                    $query->where('payment_status', 'paid');
                    if ($startDate) {
                        $query->where('created_at', '>=', $startDate);
                    }
                })
                ->select(DB::raw('sum(price * quantity) as total'))
                ->value('total') ?? 0;

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'products_count' => $category->products_count,
                    'sales' => $sales,
                ];
            })
            ->sortByDesc('sales')
            ->values();

        // Daily Revenue (last 30 days)
        $dailyRevenue = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $dailyRevenue[] = [
                'date' => $date->format('d/m'),
                'revenue' => $revenue,
            ];
        }

        // Monthly Revenue (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        // User Growth (last 12 months)
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $userGrowth[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }

        // Recent Activity
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentPayments = PaymentTransaction::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.statistics', compact(
            'generalStats',
            'ordersStats',
            'paymentStats',
            'paymentMethods',
            'topProducts',
            'categoryStats',
            'dailyRevenue',
            'monthlyRevenue',
            'userGrowth',
            'recentOrders',
            'recentPayments',
            'period'
        ));
    }
}
