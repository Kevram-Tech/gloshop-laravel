<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'total_categories' => Category::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Sales by day (last 7 days)
        $salesByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayOrders = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_amount');
            $salesByDay[$date->format('d/m')] = (float) $dayOrders;
        }

        return view('admin.dashboard.index', compact('stats', 'recentOrders', 'topProducts', 'salesByDay'));
    }
}







