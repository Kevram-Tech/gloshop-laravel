@extends('layouts.admin')

@section('title', 'Commandes - Admin')
@section('page-title', 'Gestion des Commandes')

@section('content')
<div class="mb-4">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex space-x-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="all">Tous les statuts</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En traitement</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Expédiée</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
        </select>
        <select name="payment_status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="all">Tous les paiements</option>
            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payé</option>
            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Échoué</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-search"></i> Filtrer
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paiement</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $order->user->name ?? $order->shipping_name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->user->email ?? $order->shipping_email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucune commande trouvée</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection

