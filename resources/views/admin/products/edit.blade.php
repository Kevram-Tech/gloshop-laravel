@extends('admin.layout')

@section('title', 'Modifier Produit')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-6">Modifier le Produit</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Catégorie *</label>
                    <select name="category_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Prix (FCFA) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           placeholder="Ex: 5000">
                    <p class="text-xs text-gray-500 mt-1">Montant en FCFA</p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Prix réduit (FCFA)</label>
                    <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           placeholder="Ex: 4000">
                    <p class="text-xs text-gray-500 mt-1">Montant en FCFA</p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stock *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description *</label>
                    <textarea name="description" rows="4" required
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Produit en vedette</label>
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm text-gray-600">Mettre en vedette</span>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Statut</label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm text-gray-600">Actif</span>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

