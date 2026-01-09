@extends('layouts.admin')

@section('title', 'Commandes - GloShop Admin')
@section('page-title', 'COMMANDES')
@section('page-subtitle', 'GESTION')

@section('content')
<div class="bg-white border-2 border-gray-300">
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
                @forelse($orders as $order)
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
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500 font-mono">Aucune commande</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t-2 border-gray-300">
        {{ $orders->links() }}
    </div>
</div>
@endsection

