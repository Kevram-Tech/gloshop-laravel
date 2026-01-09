@extends('layouts.admin')

@section('title', 'Modifier Catégorie - Admin')
@section('page-title', 'Modifier la Catégorie')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Image actuelle</label>
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" 
                         class="h-32 w-32 object-cover rounded mb-4">
                @endif
                <label class="block text-gray-700 text-sm font-bold mb-2">Nouvelle image</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-gray-700 text-sm">Actif</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Mettre à jour
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection

