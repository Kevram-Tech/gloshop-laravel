@extends('layouts.admin')

@section('title', 'Détails Commande - Admin')
@section('page-title', 'COMMANDE #' . $order->order_number)
@section('page-subtitle', 'DÉTAILS')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Articles commandés</h3>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            @if($item->product && $item->product->images && count($item->product->images) > 0)
                                <img src="{{ asset('storage/' . $item->product->images[0]) }}" 
                                     alt="{{ $item->product->name }}" class="h-16 w-16 object-cover rounded">
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ $item->product->name ?? 'Produit supprimé' }}</p>
                                <p class="text-sm text-gray-600">Quantité: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA</p>
                            <p class="text-sm text-gray-600">{{ number_format($item->price, 0, ',', ' ') }} FCFA / unité</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations de livraison</h3>
            <div class="space-y-2 text-gray-700">
                <p><strong>Nom:</strong> {{ $order->shipping_name }}</p>
                <p><strong>Email:</strong> {{ $order->shipping_email }}</p>
                <p><strong>Téléphone:</strong> {{ $order->shipping_phone }}</p>
                <p><strong>Adresse:</strong> {{ $order->shipping_address }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Résumé</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Sous-total</span>
                    <span class="font-semibold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statut de la commande</h3>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En traitement</option>
                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Expédiée</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Livrée</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Mettre à jour
                </button>
            </form>
            <div class="mt-4">
                <p class="text-sm text-gray-600"><strong>Paiement:</strong> 
                    <span class="px-2 py-1 text-xs rounded 
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                           ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Order Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Méthode de paiement:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
                @if($order->notes)
                    <p><strong>Notes:</strong> {{ $order->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

