@extends('layouts.admin')

@section('title', 'Catégories - GloShop Admin')
@section('page-title', 'CATÉGORIES')
@section('page-subtitle', 'GESTION')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('admin.categories.create') }}" class="bg-gray-800 text-white px-4 py-2 text-xs font-bold uppercase hover:bg-gray-900 border-2 border-gray-900">
        <i class="fas fa-plus mr-2"></i> Nouvelle Catégorie
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
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Produits</th>
                    <th class="px-4 py-2 text-left font-bold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2 font-mono">#{{ $category->id }}</td>
                    <td class="px-4 py-2">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 object-cover border border-gray-300">
                        @else
                            <div class="w-12 h-12 bg-gray-200 border border-gray-300 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $category->name }}</td>
                    <td class="px-4 py-2 font-mono">{{ $category->products_count }}</td>
                    <td class="px-4 py-2">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.categories.show', $category) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr?');">
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
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500 font-mono">Aucune catégorie</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t-2 border-gray-300">
        {{ $categories->links() }}
    </div>
</div>
@endsection

