@extends('layouts.admin')

@section('title', 'Dashboard - Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Commandes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Produits</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Utilisateurs</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Revenus Totaux</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-2">Commandes en attente</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_orders'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-2">Produits actifs</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active_products'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm mb-2">Stock faible</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['low_stock_products'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Commandes récentes</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-600">{{ $order->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                                <span class="inline-block px-2 py-1 text-xs rounded {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Aucune commande récente</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                        Voir toutes les commandes →
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Produits les plus vendus</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($topProducts as $product)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800">{{ $product->total_quantity }} vendus</p>
                                <p class="text-sm text-gray-600">{{ number_format($product->total_revenue, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Aucun produit vendu</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

