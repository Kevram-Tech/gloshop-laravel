@extends('admin.layout')

@section('title', 'Détails du Paiement')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Transaction de Paiement #{{ $payment->id }}</h1>
        <a href="{{ route('admin.payments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Informations de la Transaction</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">ID Transaction</p>
                            <p class="font-medium">#{{ $payment->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Statut</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                @if($payment->status === 'completed')
                                    Complété
                                @elseif($payment->status === 'pending')
                                    En attente
                                @elseif($payment->status === 'failed')
                                    Échoué
                                @else
                                    Annulé
                                @endif
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Montant</p>
                            <p class="font-medium text-lg text-gray-900">{{ number_format($payment->amount, 2) }} FCFA</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Méthode de Paiement</p>
                            <p class="font-medium">
                                @php
                                    $methodLabels = [
                                        'paygate_flooz' => 'Flooz',
                                        'paygate_tmoney' => 'T-Money',
                                        'card_visa' => 'Carte Visa',
                                        'card_mastercard' => 'Carte Mastercard',
                                    ];
                                    $label = $methodLabels[$payment->payment_method] ?? ucfirst(str_replace('_', ' ', $payment->payment_method));
                                @endphp
                                {{ $label }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Référence TX</p>
                            <p class="font-medium font-mono text-sm">{{ $payment->tx_reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Référence Paiement</p>
                            <p class="font-medium font-mono text-sm">{{ $payment->payment_reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Identifiant</p>
                            <p class="font-medium font-mono text-sm">{{ $payment->identifier ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date de Création</p>
                            <p class="font-medium">{{ $payment->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @if($payment->updated_at != $payment->created_at)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Dernière Mise à Jour</p>
                            <p class="font-medium">{{ $payment->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($payment->metadata && count($payment->metadata) > 0)
                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm font-medium text-gray-700 mb-3">Métadonnées</p>
                        <div class="bg-gray-50 rounded p-4">
                            <pre class="text-xs overflow-x-auto">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Information -->
            @if($payment->order)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Informations de la Commande</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Numéro de Commande</p>
                            <p class="font-medium">
                                <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-blue-600 hover:text-blue-900">
                                    #{{ $payment->order->order_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Statut de la Commande</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $payment->order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                   ($payment->order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($payment->order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($payment->order->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Statut de Paiement</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $payment->order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($payment->order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($payment->order->payment_status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Montant Total</p>
                            <p class="font-medium">{{ number_format($payment->order->total_amount, 2) }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- User Information -->
            @if($payment->user)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Informations du Client</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nom</p>
                            <p class="font-medium">{{ $payment->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="font-medium">{{ $payment->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">ID Utilisateur</p>
                            <p class="font-medium">#{{ $payment->user->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Rôle</p>
                            <span class="px-2 py-1 text-xs rounded-full {{ $payment->user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $payment->user->is_admin ? 'Admin' : 'Client' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Status Update -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Mettre à jour le Statut</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.payments.update', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau Statut</label>
                            <select name="status" class="w-full border rounded px-3 py-2" required>
                                <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Échoué</option>
                                <option value="cancelled" {{ $payment->status === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-save mr-2"></i>Mettre à jour
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Actions Rapides</h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="block w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                        <i class="fas fa-shopping-cart mr-2"></i>Voir la Commande
                    </a>
                    @if($payment->user)
                    <a href="{{ route('admin.users.edit', $payment->user_id) }}" class="block w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                        <i class="fas fa-user mr-2"></i>Voir le Client
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

