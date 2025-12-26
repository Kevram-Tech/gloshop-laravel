@extends('admin.layout')

@section('title', 'Nouvelle Catégorie')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-6">Nouvelle Catégorie</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                           placeholder="Auto-généré si vide">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour générer automatiquement</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Statut</label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm text-gray-600">Actif</span>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i>Créer la catégorie
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

