<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $salesByPeriod = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
        ->where('status', 'completed')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(30)
        ->get();

        $topProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        $stockStats = [
            'low_stock' => Product::where('stock', '<', 10)->count(),
            'out_of_stock' => Product::where('stock', '=', 0)->count(),
            'total_products' => Product::count(),
        ];

        return view('admin.statistics.index', compact('salesByPeriod', 'topProducts', 'stockStats'));
    }
}

