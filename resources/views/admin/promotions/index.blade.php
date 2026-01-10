@extends('layouts.admin')

@section('title', 'Promotions - Admin')
@section('page-title', 'PROMOTIONS')
@section('page-subtitle', 'GESTION')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-4">
        <form method="GET" action="{{ route('admin.promotions.index') }}" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactives</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i> Filtrer
            </button>
        </form>
    </div>
    <a href="{{ route('admin.promotions.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>Nouvelle Promotion
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisations</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($promotions as $promotion)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($promotion->image)
                            <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" 
                                 class="h-16 w-16 object-cover rounded">
                        @else
                            <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $promotion->title }}</div>
                        <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($promotion->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                            {{ ucfirst($promotion->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($promotion->type === 'percentage')
                            {{ $promotion->discount_value }}%
                        @elseif($promotion->type === 'fixed')
                            {{ number_format($promotion->discount_value, 0, ',', ' ') }} FCFA
                        @else
                            Livraison gratuite
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $promotion->start_date->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-400">au {{ $promotion->end_date->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $promotion->usage_count }} / {{ $promotion->usage_limit ?? '∞' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded {{ $promotion->is_active && $promotion->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $promotion->is_active && $promotion->isActive() ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.promotions.edit', $promotion) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion?');">
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
                        Aucune promotion trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $promotions->links() }}
    </div>
</div>
@endsection

