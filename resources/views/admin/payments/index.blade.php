@extends('admin.layout')

@section('title', 'Paiements')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-6">Gestion des Paiements</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Complétés</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">En attente</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Échoués</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Annulés</p>
            <p class="text-2xl font-bold text-gray-600">{{ $stats['cancelled'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Montant Total</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_amount'], 2) }} FCFA</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex gap-4 flex-wrap">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Rechercher (référence, client, commande)..." 
                   class="flex-1 min-w-64 border rounded px-4 py-2">
            <select name="status" class="border rounded px-4 py-2">
                <option value="all">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
            </select>
            <select name="payment_method" class="border rounded px-4 py-2">
                <option value="all">Toutes les méthodes</option>
                <option value="paygate_flooz" {{ request('payment_method') == 'paygate_flooz' ? 'selected' : '' }}>Flooz</option>
                <option value="paygate_tmoney" {{ request('payment_method') == 'paygate_tmoney' ? 'selected' : '' }}>T-Money</option>
                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Carte</option>
            </select>
            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-search mr-2"></i>Filtrer
            </button>
            @if(request('search') || request('status') || request('payment_method'))
            <a href="{{ route('admin.payments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Réinitialiser
            </a>
            @endif
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $payment->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $payment->user->name ?? 'N/A' }}<br>
                        <span class="text-gray-500 text-xs">{{ $payment->user->email ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-blue-600 hover:text-blue-900">
                            #{{ $payment->order->order_number ?? $payment->order_id }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ number_format($payment->amount, 2) }} FCFA
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="text-xs">
                            @if($payment->tx_reference)
                                <div><strong>TX:</strong> {{ $payment->tx_reference }}</div>
                            @endif
                            @if($payment->payment_reference)
                                <div><strong>Ref:</strong> {{ $payment->payment_reference }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $payment->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">Aucune transaction de paiement trouvée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection

