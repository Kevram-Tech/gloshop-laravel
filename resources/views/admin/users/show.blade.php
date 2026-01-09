@extends('layouts.admin')

@section('title', 'Détails Utilisateur - Admin')
@section('page-title', 'Détails de l\'Utilisateur')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- User Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Commandes ({{ $user->orders->count() }})</h3>
            <div class="space-y-4">
                @forelse($user->orders as $order)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                            <span class="px-2 py-1 text-xs rounded 
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Aucune commande</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- User Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-4">
                <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl font-semibold mx-auto mb-4">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <p><strong>Inscrit le:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                <p><strong>Total commandes:</strong> {{ $user->orders->count() }}</p>
                <p><strong>Total dépensé:</strong> {{ number_format($user->orders->sum('total_amount'), 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <!-- Addresses -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Adresses ({{ $user->addresses->count() }})</h3>
            <div class="space-y-3">
                @forelse($user->addresses as $address)
                    <div class="p-3 bg-gray-50 rounded-lg text-sm">
                        <p class="font-semibold">{{ $address->title ?? $address->full_name }}</p>
                        <p class="text-gray-600">{{ $address->address }}</p>
                        <p class="text-gray-600">{{ $address->city }}, {{ $address->country }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-2">Aucune adresse</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

