@extends('admin.layout')

@section('title', 'Détails de la Commande')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Commande #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Articles de la commande</h2>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        @if($item->product && !empty($item->product->images) && is_array($item->product->images))
                                            <img src="{{ strpos($item->product->images[0], 'http') === 0 ? $item->product->images[0] : asset('storage/' . $item->product->images[0]) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-12 h-12 object-cover rounded mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Produit supprimé' }}</div>
                                            @if($item->size || $item->color)
                                            <div class="text-xs text-gray-500">
                                                @if($item->size) Taille: {{ $item->size }} @endif
                                                @if($item->color) Couleur: {{ $item->color }} @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ number_format($item->price, 2) }} FCFA
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                    {{ number_format($item->price * $item->quantity, 2) }} FCFA
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-bold">Total:</td>
                                <td class="px-4 py-4 text-lg font-bold text-gray-900">
                                    {{ number_format($order->total_amount, 2) }} FCFA
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Informations de livraison</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        <p><strong>Nom:</strong> {{ $order->shipping_name ?? $order->user->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->shipping_email ?? $order->user->email ?? 'N/A' }}</p>
                        <p><strong>Téléphone:</strong> {{ $order->shipping_phone ?? 'N/A' }}</p>
                        <p><strong>Adresse:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                    </div>
                    @if($order->notes)
                    <div class="mt-4 pt-4 border-t">
                        <p><strong>Notes:</strong></p>
                        <p class="text-gray-600">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="space-y-6">
            <!-- Status Update -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Statut de la commande</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status" class="w-full border rounded px-3 py-2">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>En traitement</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Expédiée</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Livrée</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut de paiement</label>
                            <select name="payment_status" class="w-full border rounded px-3 py-2">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Payée</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Mettre à jour
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Informations</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Numéro de commande</p>
                        <p class="font-medium">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Client</p>
                        <p class="font-medium">{{ $order->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user->email ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Méthode de paiement</p>
                        <p class="font-medium">{{ $order->payment_method ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date de commande</p>
                        <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut actuel</p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                               ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Paiement</p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

