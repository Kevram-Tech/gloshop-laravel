@extends('layouts.admin')

@section('title', 'Produits - Admin')
@section('page-title', 'Gestion des Produits')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
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
    <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>Nouveau Produit
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" 
                                 class="h-16 w-16 object-cover rounded">
                        @else
                            <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                        @if($product->discount_price)
                            <div class="text-sm text-red-600">{{ number_format($product->discount_price, 0, ',', ' ') }} FCFA</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded {{ $product->stock < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
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
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Aucun produit trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection

