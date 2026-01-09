@extends('layouts.admin')

@section('title', 'Modifier Produit - Admin')
@section('page-title', 'MODIFIER PRODUIT')
@section('page-subtitle', 'ÉDITION')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Catégorie *</label>
                <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description *</label>
                <textarea name="description" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Prix *</label>
                <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Prix réduit</label>
                <input type="number" name="discount_price" step="0.01" value="{{ old('discount_price', $product->discount_price) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">SKU *</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Stock *</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">Images existantes</label>
                <div class="grid grid-cols-4 gap-4 mb-4">
                    @if($product->images)
                        @foreach($product->images as $index => $image)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $image) }}" alt="Image {{ $index + 1 }}" 
                                     class="w-full h-32 object-cover rounded">
                                <input type="hidden" name="existing_images[]" value="{{ $image }}">
                            </div>
                        @endforeach
                    @endif
                </div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Ajouter de nouvelles images</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-gray-700 text-sm">Produit en vedette</span>
                </label>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-gray-700 text-sm">Actif</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Mettre à jour
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection

