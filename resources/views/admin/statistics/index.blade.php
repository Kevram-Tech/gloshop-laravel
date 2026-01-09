@extends('layouts.admin')

@section('title', 'Statistiques - GloShop Admin')
@section('page-title', 'STATISTIQUES')
@section('page-subtitle', 'ANALYTICS')

@section('content')
<div class="space-y-6">
    <!-- Stock Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border-2 border-gray-300 p-4">
            <p class="text-xs text-gray-500 font-mono uppercase mb-1">Stock Faible</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stockStats['low_stock'] }}</p>
        </div>
        <div class="bg-white border-2 border-gray-300 p-4">
            <p class="text-xs text-gray-500 font-mono uppercase mb-1">Rupture de Stock</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stockStats['out_of_stock'] }}</p>
        </div>
        <div class="bg-white border-2 border-gray-300 p-4">
            <p class="text-xs text-gray-500 font-mono uppercase mb-1">Total Produits</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stockStats['total_products'] }}</p>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white border-2 border-gray-300">
        <div class="p-4 border-b-2 border-gray-300">
            <h3 class="text-sm font-bold text-gray-900 uppercase">Produits les Plus Vendus</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-100 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Produit</th>
                        <th class="px-4 py-2 text-left font-bold text-gray-900">Quantité Vendue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $product)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2">{{ $product->name }}</td>
                        <td class="px-4 py-2 font-mono">{{ $product->total_sold ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-4 text-center text-gray-500 font-mono">Aucune donnée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

