@extends('layouts.admin')

@section('title', 'Dashboard - GloShop Admin')
@section('page-title', 'DASHBOARD')
@section('page-subtitle', 'OVERVIEW')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border-2 border-gray-300 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-mono uppercase mb-1">Total Commandes</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-800 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border-2 border-gray-300 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-mono uppercase mb-1">Total Produits</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-800 flex items-center justify-center">
                    <i class="fas fa-box text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border-2 border-gray-300 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-mono uppercase mb-1">Total Utilisateurs</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-800 flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border-2 border-gray-300 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-mono uppercase mb-1">Revenus Total</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 bg-gray-800 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white border-2 border-gray-300">
        <div class="p-4 border-b-2 border-gray-300">
            <h3 class="text-sm font-bold text-gray-900 uppercase">Commandes Récentes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-100 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">ID</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Client</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Montant</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Statut</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Date</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_orders ?? [] as $order)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2 font-mono">#{{ $order->id }}</td>
                        <td class="px-4 py-2">{{ $order->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2 font-mono">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs font-mono bg-gray-200 text-gray-800 border border-gray-300">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 font-mono">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500 font-mono">Aucune commande récente</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

