@extends('layouts.admin')

@section('title', 'Modifier Code Promo - Admin')
@section('page-title', 'MODIFIER CODE PROMO')
@section('page-subtitle', 'ÉDITION')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.promo-codes.update', $promoCode) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Code *</label>
                <input type="text" name="code" value="{{ old('code', $promoCode->code) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono uppercase">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
                <input type="text" name="name" value="{{ old('name', $promoCode->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $promoCode->description) }}</textarea>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Type *</label>
                <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="percentage" {{ old('type', $promoCode->type) == 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                    <option value="fixed" {{ old('type', $promoCode->type) == 'fixed' ? 'selected' : '' }}>Montant fixe</option>
                    <option value="free_shipping" {{ old('type', $promoCode->type) == 'free_shipping' ? 'selected' : '' }}>Livraison gratuite</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Valeur de réduction *</label>
                <input type="number" name="discount_value" step="0.01" value="{{ old('discount_value', $promoCode->discount_value) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Montant minimum d'achat</label>
                <input type="number" name="min_purchase_amount" step="0.01" value="{{ old('min_purchase_amount', $promoCode->min_purchase_amount) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Montant maximum de réduction</label>
                <input type="number" name="max_discount_amount" step="0.01" value="{{ old('max_discount_amount', $promoCode->max_discount_amount) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Date de début *</label>
                <input type="datetime-local" name="start_date" value="{{ old('start_date', $promoCode->start_date->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Date de fin *</label>
                <input type="datetime-local" name="end_date" value="{{ old('end_date', $promoCode->end_date->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Limite d'utilisation totale</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $promoCode->usage_limit) }}" min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Limite par utilisateur</label>
                <input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $promoCode->usage_limit_per_user) }}" min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">Catégories applicables</label>
                <select name="applicable_categories[]" multiple
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, old('applicable_categories', $promoCode->applicable_categories ?? [])) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">Produits applicables</label>
                <select name="applicable_products[]" multiple
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ in_array($product->id, old('applicable_products', $promoCode->applicable_products ?? [])) ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promoCode->is_active) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-gray-700 text-sm">Actif</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
            <a href="{{ route('admin.promo-codes.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection






