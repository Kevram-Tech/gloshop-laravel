@extends('layouts.admin')

@section('title', 'Produits - GloShop Admin')
@section('page-title', 'PRODUITS')
@section('page-subtitle', 'GESTION')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('admin.products.create') }}" class="bg-gray-800 text-white px-4 py-2 text-xs font-bold uppercase hover:bg-gray-900 border-2 border-gray-900">
        <i class="fas fa-plus mr-2"></i> Nouveau Produit
    </a>
</div>

<div class="bg-white border-2 border-gray-300">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">ID</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Image</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Nom</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Catégorie</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Prix</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Stock</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2 font-mono">#{{ $product->id }}</td>
                    <td class="px-4 py-2">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover border border-gray-300">
                        @else
                            <div class="w-12 h-12 bg-gray-200 border border-gray-300 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 font-mono">{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 font-mono">{{ $product->stock }}</td>
                    <td class="px-4 py-2">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.products.show', $product) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500 font-mono">Aucun produit</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t-2 border-gray-300">
        {{ $products->links() }}
    </div>
</div>
@endsection

