@extends('layouts.admin')

@section('title', 'Codes Promos - Admin')
@section('page-title', 'CODES PROMOS')
@section('page-subtitle', 'GESTION')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-4">
        <form method="GET" action="{{ route('admin.promo-codes.index') }}" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </form>
    </div>
    <a href="{{ route('admin.promo-codes.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>Nouveau Code Promo
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisations</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($promoCodes as $promoCode)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono font-bold text-gray-900">{{ $promoCode->code }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $promoCode->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($promoCode->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                            {{ ucfirst($promoCode->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($promoCode->type === 'percentage')
                            {{ $promoCode->discount_value }}%
                            @if($promoCode->max_discount_amount)
                                <div class="text-xs text-gray-500">Max: {{ number_format($promoCode->max_discount_amount, 0, ',', ' ') }} FCFA</div>
                            @endif
                        @elseif($promoCode->type === 'fixed')
                            {{ number_format($promoCode->discount_value, 0, ',', ' ') }} FCFA
                        @else
                            Livraison gratuite
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $promoCode->start_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-400">au {{ $promoCode->end_date->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $promoCode->usage_count }} / {{ $promoCode->usage_limit ?? '∞' }}
                        @if($promoCode->usage_limit_per_user)
                            <div class="text-xs text-gray-400">({{ $promoCode->usage_limit_per_user }}/user)</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded {{ $promoCode->is_active && $promoCode->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $promoCode->is_active && $promoCode->isValid() ? 'Valide' : 'Invalide' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.promo-codes.edit', $promoCode) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.promo-codes.destroy', $promoCode) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce code promo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Aucun code promo trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $promoCodes->links() }}
    </div>
</div>
@endsection

